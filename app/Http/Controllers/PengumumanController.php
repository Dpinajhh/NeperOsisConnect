<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    /** Daftar pengumuman yang relevan untuk user. */
    public function index(): View
    {
        $user = auth()->user();

        $pengumuman = Pengumuman::terbit()
            ->untuk($user)
            ->with(['penulis', 'kategori'])
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->paginate(10);

        // Tandai yang sudah & belum dibaca
        $dibaca = $user->pembaca()->pluck('pengumuman.id')->all();
        $pengumuman->getCollection()->each(function (Pengumuman $p) use ($dibaca) {
            $p->sudah_dibaca = in_array($p->id, $dibaca, true);
        });

        return view('pengumuman', compact('pengumuman'));
    }

    public function show(Pengumuman $pengumuman): View
    {
        abort_unless(in_array($pengumuman->target, ['semua'], true)
            || ($pengumuman->target === 'kelas' && $pengumuman->kelas_id === auth()->user()->kelas_id)
            || ($pengumuman->target === 'jurusan' && $pengumuman->jurusan_id === auth()->user()->kelas?->jurusan_id),
            403);

        $pengumuman->increment('dilihat');

        // Tandai dibaca
        auth()->user()->pembaca()->syncWithoutDetaching([$pengumuman->id]);

        $user = auth()->user();
        $user->pembaca()->updateExistingPivot($pengumuman->id, ['dibaca_at' => now()]);

        return view('pengumuman-detail', compact('pengumuman'));
    }

    // ─── Kelola (admin & guru) ───────────────────────────

    public function kelola(Request $request): View
    {
        $daftar = Pengumuman::with(['penulis', 'kategori'])
            ->latest('is_pinned')
            ->latest()
            ->paginate(10);

        $edit = $request->filled('edit') ? Pengumuman::find($request->integer('edit')) : null;

        return view('admin.pengumuman', [
            'pengumuman' => $daftar,
            'edit' => $edit,
            'kategori' => Kategori::where('tipe', 'pengumuman')->orderBy('nama')->get(),
            'kelas' => Kelas::orderBy('nama')->get(),
            'jurusan' => Jurusan::orderBy('kode')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        Pengumuman::create([
            ...$validated,
            'slug' => $this->slugUnik($validated['judul']),
            'user_id' => $request->user()->id,
            'published_at' => $request->input('published_at') ? $request->date('published_at') : now(),
        ]);

        return back()->with('sukses', 'Pengumuman diterbitkan.');
    }

    public function update(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
        $validated = $this->validasi($request);

        $pengumuman->update([
            ...$validated,
            'slug' => $pengumuman->judul === $validated['judul'] ? $pengumuman->slug : $this->slugUnik($validated['judul']),
        ]);

        return back()->with('sukses', 'Pengumuman diperbarui.');
    }

    public function sematkan(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->update(['is_pinned' => ! $pengumuman->is_pinned]);

        return back()->with('sukses', $pengumuman->is_pinned ? 'Pengumuman disematkan.' : 'Sematkan dilepas.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->delete();

        return back()->with('sukses', 'Pengumuman dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'target' => ['required', 'in:semua,kelas,jurusan'],
            'kelas_id' => ['nullable', 'required_if:target,kelas', 'exists:kelas,id'],
            'jurusan_id' => ['nullable', 'required_if:target,jurusan', 'exists:jurusan,id'],
            'is_pinned' => ['nullable', 'boolean'],
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'konten.required' => 'Isi pengumuman wajib diisi.',
            'kelas_id.required_if' => 'Pilih kelas tujuan.',
            'jurusan_id.required_if' => 'Pilih jurusan tujuan.',
        ]);
    }

    private function slugUnik(string $judul): string
    {
        $dasar = Str::slug($judul);
        $slug = $dasar;
        $i = 1;

        while (Pengumuman::where('slug', $slug)->exists()) {
            $slug = $dasar.'-'.++$i;
        }

        return $slug;
    }
}
