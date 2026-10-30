<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Notifikasi;
use App\Models\Post;
use App\Models\Topik;
use App\Models\User;
use App\Services\TrendingTopicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private readonly TrendingTopicService $trending) {}

    /**
     * Simpan postingan baru: teks, gambar (opsional), & hashtag otomatis.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasiPost($request, denganGambar: true);

        $user = $request->user();

        // Siswa hanya boleh memilih visibilitas sesuai kelas/jurusannya
        if ($galat = $this->galatVisibilitas($validated['visibilitas'], $user)) {
            return back()->withErrors(['visibilitas' => $galat]);
        }

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('posts', 'public');
        }

        $post = $user->posts()->create([
            'konten' => $validated['konten'],
            'gambar' => $validated['gambar'] ?? null,
            'visibilitas' => $validated['visibilitas'],
        ]);

        $this->sinkronTopik($post);

        $this->trending->lupakanCache();

        return back()->with('sukses', 'Postinganmu sudah dibagikan! 🎉');
    }

    /** Halaman satu postingan (untuk "lihat semua komentar" & bagikan). */
    public function show(Post $post): View
    {
        abort_unless($this->bolehLihat($post), 403);

        $post->load(['user.kelas.jurusan', 'komentar.user', 'komentar.balasan.user', 'topik']);
        $post->sudah_disukai = $post->disukaiOlehUser(auth()->id());

        return view('post-detail', compact('post'));
    }

    /** Form ubah postingan — hanya pemilik postingan. */
    public function edit(Post $post): View
    {
        abort_unless($post->user_id === auth()->id(), 403);

        return view('post-edit', compact('post'));
    }

    /** Simpan perubahan isi & visibilitas postingan milik sendiri. */
    public function update(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->user_id === $request->user()->id, 403);

        $validated = $this->validasiPost($request);

        if ($galat = $this->galatVisibilitas($validated['visibilitas'], $request->user())) {
            return back()->withErrors(['visibilitas' => $galat]);
        }

        $post->update([
            'konten' => $validated['konten'],
            'visibilitas' => $validated['visibilitas'],
        ]);

        // Hashtag bisa berubah saat diedit: hitung ulang topik terkait.
        $this->sinkronTopik($post);
        $this->trending->lupakanCache();

        return redirect()
            ->route('posts.show', $post)
            ->with('sukses', 'Postingan berhasil diperbarui.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->id === $post->user_id || $user->isPengelola(), 403);

        // Kurangi counter post pada tiap topik terkait
        Topik::whereIn('id', $post->topik()->pluck('topik.id'))->decrement('jumlah_post');

        $post->delete();

        $this->trending->lupakanCache();

        return back()->with('sukses', 'Postingan dihapus.');
    }

    /**
     * Toggle suka. Mengembalikan JSON agar tombol bisa
     * diperbarui tanpa reload (gaya aplikasi modern).
     */
    public function suka(Post $post): JsonResponse
    {
        // Jangan izinkan interaksi dengan post yang tidak boleh dilihat
        abort_unless($this->bolehLihat($post), 403);

        $user = Auth::user();
        $sudahSuka = $post->disukaiOlehUser($user->id);

        if ($sudahSuka) {
            $post->disukaiOleh()->detach($user->id);
            $post->decrement('jumlah_suka');
            $disukai = false;
        } else {
            $post->disukaiOleh()->attach($user->id);
            $post->increment('jumlah_suka');
            $disukai = true;

            // Notifikasi ke pemilik post (bukan ke diri sendiri)
            if ($post->user_id !== $user->id) {
                Notifikasi::create([
                    'user_id' => $post->user_id,
                    'tipe' => 'suka',
                    'judul' => 'Postinganmu disukai',
                    'pesan' => $user->name.' menyukai postinganmu.',
                    'url' => route('posts.show', $post),
                ]);
            }
        }

        return response()->json([
            'disukai' => $disukai,
            'jumlah_suka' => $post->fresh()->jumlah_suka,
        ]);
    }

    public function komentar(Request $request, Post $post): JsonResponse
    {
        abort_unless($this->bolehLihat($post), 403);

        $validated = $request->validate([
            'konten' => ['required', 'string', 'max:500'],
            // Balasan harus berada pada post yang sama (cegah balasan lintas post)
            'parent_id' => [
                'nullable',
                Rule::exists('komentar', 'id')
                    ->where('post_id', $post->id)
                    ->whereNull('deleted_at'),
            ],
        ], [
            'konten.required' => 'Komentar tidak boleh kosong.',
        ]);

        $komentar = $post->komentar()->create([
            'user_id' => Auth::id(),
            'konten' => $validated['konten'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $post->increment('jumlah_komentar');
        $komentar->load('user');

        // Notifikasi: pemilik post & pemilik komentar yang dibalas (bukan diri sendiri)
        $tujuan = collect([$post->user_id, $komentar->parent?->user_id])->filter()
            ->unique()->reject(fn ($id) => $id === Auth::id());

        foreach ($tujuan as $userId) {
            Notifikasi::create([
                'user_id' => $userId,
                'tipe' => 'komentar',
                'judul' => 'Komentar baru',
                'pesan' => Auth::user()->name.' mengomentari postingan: "'.Str::limit($komentar->konten, 60).'"',
                'url' => route('posts.show', $post),
            ]);
        }

        $this->trending->lupakanCache();

        return response()->json([
            'id' => $komentar->id,
            'konten' => $komentar->konten,
            'nama' => $komentar->user->name,
            'avatar' => $komentar->user->avatar_url,
            'inisial_role' => $komentar->user->role,
            'waktu' => $komentar->created_at->diffForHumans(),
            'parent_id' => $komentar->parent_id,
        ]);
    }

    /** Komentar milik sendiri (atau moderasi pengelola) bisa dihapus. */
    public function hapusKomentar(Request $request, Post $post, Komentar $komentar): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->id === $komentar->user_id || $user->isPengelola(), 403);

        // Komentar harus benar-benar milik post pada URL; kalau tidak,
        // counter post lain bisa dikurangi lewat ID komentar sendiri.
        abort_unless((int) $komentar->post_id === (int) $post->id, 404);

        // Ikut menghapus balasan lewat cascade DB — counter disesuaikan
        $jumlah = $komentar->balasan()->count() + 1;

        $komentar->delete();
        $post->decrement('jumlah_komentar', $jumlah);

        $this->trending->lupakanCache();

        return response()->json([
            'dihapus' => true,
            'jumlah_komentar' => $post->fresh()->jumlah_komentar,
            'jumlah_hilang' => $jumlah,
        ]);
    }

    /** ─── Helper internal ─────────────────────────────── */

    /** Validasi isi postingan (dipakai saat buat & ubah). */
    private function validasiPost(Request $request, bool $denganGambar = false): array
    {
        $aturan = [
            'konten' => ['required', 'string', 'max:2000'],
            'visibilitas' => ['required', 'in:sekolah,kelas,jurusan'],
        ];
        $pesan = [
            'konten.required' => 'Tulis sesuatu dulu sebelum membagikan.',
            'visibilitas.required' => 'Pilih siapa yang boleh melihat postingan.',
        ];

        if ($denganGambar) {
            $aturan['gambar'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
            $pesan['gambar.image'] = 'File harus berupa gambar.';
            $pesan['gambar.max'] = 'Ukuran gambar maksimal 2 MB.';
        }

        return $request->validate($aturan, $pesan);
    }

    /** Pesan galat bila visibilitas yang dipilih belum didukung data siswa. */
    private function galatVisibilitas(string $visibilitas, User $user): ?string
    {
        if ($visibilitas === 'kelas' && ! $user->kelas_id) {
            return 'Kamu belum punya kelas, pilih "Seluruh sekolah".';
        }
        if ($visibilitas === 'jurusan' && ! $user->kelas?->jurusan_id) {
            return 'Kamu belum punya jurusan, pilih "Seluruh sekolah".';
        }

        return null;
    }

    private function bolehLihat(Post $post): bool
    {
        $user = Auth::user();

        if ($post->visibilitas === 'sekolah') {
            return true;
        }
        if ($post->visibilitas === 'kelas' && $user->kelas_id) {
            return $post->user->kelas_id === $user->kelas_id;
        }
        if ($post->visibilitas === 'jurusan' && $user->kelas?->jurusan_id) {
            return $post->user->kelas?->jurusan_id === $user->kelas->jurusan_id;
        }

        return false;
    }

    /** Tarik hashtag dari konten post, buat/temukan topik, lalu tautkan. */
    private function sinkronTopik(Post $post): void
    {
        $ids = collect(Post::ekstrakTopik($post->konten))
            ->map(fn (string $nama) => Topik::firstOrCreate(
                ['nama' => $nama],
                ['slug' => Str::slug($nama)]
            )->id)
            ->all();

        $lama = $post->topik()->pluck('topik.id');
        $baru = collect($ids);

        $post->topik()->sync($ids);

        // Saat diedit, topik yang tetap tidak boleh dihitung ulang.
        $dihapus = $lama->diff($baru);
        $ditambah = $baru->diff($lama);

        if ($dihapus->isNotEmpty()) {
            Topik::whereIn('id', $dihapus)->decrement('jumlah_post');
        }
        if ($ditambah->isNotEmpty()) {
            Topik::whereIn('id', $ditambah)->increment('jumlah_post');
        }
    }
}
