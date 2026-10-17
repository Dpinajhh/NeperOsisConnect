<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BeritaController extends Controller
{
    /** Daftar berita terbit (publik untuk terautentikasi). */
    public function index(): View
    {
        $berita = Berita::terbit()
            ->with(['penulis', 'kategori'])
            ->orderByDesc('is_headline')
            ->latest('published_at')
            ->paginate(9);

        return view('berita', compact('berita'));
    }

    public function show(Berita $berita): View
    {
        // Draft & berita terjadwal hanya bisa dibuka lewat panel pengelola
        abort_unless($berita->status === 'published' && $berita->published_at && $berita->published_at->isPast(), 404);

        $berita->increment('dilihat');

        $lainnya = Berita::terbit()
            ->whereKeyNot($berita->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('berita-detail', compact('berita', 'lainnya'));
    }

    // ─── Kelola (admin & guru) ───────────────────────────

    public function kelola(Request $request): View
    {
        $daftar = Berita::with('kategori', 'penulis')->latest()->paginate(10);
        $edit = $request->filled('edit') ? Berita::find($request->integer('edit')) : null;

        return view('admin.berita', [
            'berita' => $daftar,
            'edit' => $edit,
            'kategori' => Kategori::where('tipe', 'berita')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
            $validated['cover'] = $request->file('cover')->store('berita', 'public');
        }

        $publish = $request->input('status') === 'published';

        Berita::create([
            ...$validated,
            'slug' => $this->slugUnik($validated['judul']),
            'user_id' => $request->user()->id,
            'published_at' => $publish ? now() : null,
        ]);

        return back()->with('sukses', $publish ? 'Berita diterbitkan.' : 'Berita disimpan sebagai draft.');
    }

    public function update(Request $request, Berita $berita): RedirectResponse
    {
        $validated = $this->validasi($request);

        if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
            $validated['cover'] = $request->file('cover')->store('berita', 'public');
        }

        $publish = ($validated['status'] ?? 'draft') === 'published';
        $validated['published_at'] = $publish ? ($berita->published_at ?? now()) : null;
        $validated['slug'] = $berita->judul === $validated['judul'] ? $berita->slug : $this->slugUnik($validated['judul']);

        $berita->update($validated);

        return back()->with('sukses', 'Berita diperbarui.');
    }

    public function destroy(Berita $berita): RedirectResponse
    {
        $berita->delete();

        return back()->with('sukses', 'Berita dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'ringkasan' => ['required', 'string', 'max:300'],
            'konten' => ['required', 'string'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
            'is_headline' => ['nullable', 'boolean'],
        ], [
            'judul.required' => 'Judul berita wajib diisi.',
            'ringkasan.required' => 'Ringkasan berita wajib diisi.',
            'konten.required' => 'Isi berita wajib diisi.',
            'cover.image' => 'Cover harus berupa gambar.',
            'cover.max' => 'Ukuran cover maksimal 2 MB.',
        ]);
    }

    private function slugUnik(string $judul): string
    {
        $dasar = Str::slug($judul);
        $slug = $dasar;
        $i = 1;

        while (Berita::where('slug', $slug)->exists()) {
            $slug = $dasar.'-'.++$i;
        }

        return $slug;
    }
}
