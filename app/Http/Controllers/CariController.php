<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Pengumuman;
use App\Models\Post;
use App\Models\Topik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CariController extends Controller
{
    /**
     * Pencarian global: postingan, berita, pengumuman, topik & siswa.
     * Semua hasil sudah difilter sesuai visibilitas/otorisasi.
     */
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->string('q'));

        // Belum ada kata kunci → tampilkan halaman pencarian kosong
        if ($q === '') {
            return view('cari', [
                'q' => '',
                'total' => 0,
                'posts' => collect(),
                'berita' => collect(),
                'pengumuman' => collect(),
                'topik' => collect(),
                'pengguna' => collect(),
            ]);
        }

        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:60'],
        ], [
            'q.min' => 'Kata kunci minimal 2 huruf.',
            'q.max' => 'Kata kunci maksimal 60 huruf.',
        ]);

        $user = $request->user();

        // ─── Postingan ──────────────────────────────────
        $posts = Post::query()
            ->with(['user.kelas.jurusan', 'topik'])
            ->where('konten', 'like', "%{$q}%")
            ->when(! $user->isPengelola(), function ($query) use ($user) {
                $query->where(function ($sub) use ($user) {
                    $sub->where('visibilitas', 'sekolah');

                    if ($user->kelas_id) {
                        $sub->orWhere(function ($s2) use ($user) {
                            $s2->where('visibilitas', 'kelas')->whereHas('user', fn ($u) => $u->where('kelas_id', $user->kelas_id));
                        });
                    }

                    if ($user->kelas?->jurusan_id) {
                        $sub->orWhere(function ($s3) use ($user) {
                            $s3->where('visibilitas', 'jurusan')->whereHas('user.kelas', fn ($u) => $u->where('jurusan_id', $user->kelas->jurusan_id));
                        });
                    }
                });
            })
            ->latest()
            ->take(12)
            ->get();

        // ─── Berita ─────────────────────────────────────
        $berita = Berita::query()
            ->where('status', 'published')
            ->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                    ->orWhere('ringkasan', 'like', "%{$q}%")
                    ->orWhere('konten', 'like', "%{$q}%");
            })
            ->with('kategori')
            ->latest('published_at')
            ->take(6)
            ->get();

        // ─── Pengumuman ─────────────────────────────────
        $pengumuman = Pengumuman::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($query) use ($q) {
                $query->where('judul', 'like', "%{$q}%")
                    ->orWhere('konten', 'like', "%{$q}%");
            })
            ->when(! $user->isPengelola(), fn ($query) => $query->untuk($user))
            ->with('kategori')
            ->latest('published_at')
            ->take(6)
            ->get();

        // ─── Topik / hashtag ────────────────────────────
        $topik = Topik::where('nama', 'like', "%{$q}%")
            ->orWhere('slug', 'like', '%'.str_replace(' ', '-', strtolower($q)).'%')
            ->orderByDesc('jumlah_post')
            ->take(6)
            ->get();

        // ─── Pengguna ───────────────────────────────────
        $pengguna = User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            })
            ->where('is_active', true)
            ->where('is_banned', false)
            ->with('kelas.jurusan')
            ->take(8)
            ->get();

        $total = $posts->count() + $berita->count() + $pengumuman->count()
            + $topik->count() + $pengguna->count();

        return view('cari', compact(
            'q', 'posts', 'berita', 'pengumuman', 'topik', 'pengguna', 'total'
        ));
    }
}
