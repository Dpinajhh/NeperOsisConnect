<?php

namespace App\Http\Controllers;

use App\Models\Kepengurusan;
use App\Models\ProfilOsis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilOsisController extends Controller
{
    /** Halaman publik Profil OSIS. */
    public function index(): View
    {
        $profil = ProfilOsis::ambil();
        $pengurus = Kepengurusan::urut()->get();

        return view('profil-osis', compact('profil', 'pengurus'));
    }

    // ─── Kelola (admin & guru) ─────────────────────────

    public function edit(): View
    {
        return view('admin.profil-osis-edit', [
            'profil' => ProfilOsis::ambil(),
            'pengurus' => Kepengurusan::urut()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode' => ['required', 'string', 'max:20'],
            'visi' => ['required', 'string', 'max:2000'],
            'misi' => ['required', 'string', 'max:3000'],
            'sejarah' => ['nullable', 'string', 'max:3000'],
            'sambutuan' => ['nullable', 'string', 'max:2000'],
            'sambutuan_pengirim' => ['nullable', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'instagram' => ['nullable', 'string', 'max:60'],
        ], [
            'periode.required' => 'Periode kepengurusan wajib diisi.',
            'visi.required' => 'Visi OSIS wajib diisi.',
            'misi.required' => 'Misi OSIS wajib diisi.',
        ]);

        ProfilOsis::ambil()->update($validated);

        return back()->with('sukses', 'Profil OSIS diperbarui.');
    }

    public function simpanPengurus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:100'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nama.required' => 'Nama pengurus wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('kepengurusan', 'public');
        }

        Kepengurusan::create([
            'nama' => $validated['nama'],
            'jabatan' => $validated['jabatan'],
            'bidang' => $validated['bidang'] ?? null,
            'foto' => $validated['foto'] ?? null,
            'urutan' => (int) Kepengurusan::max('urutan') + 1,
        ]);

        return back()->with('sukses', 'Pengurus OSIS ditambahkan.');
    }

    public function hapusPengurus(Kepengurusan $pengurus): RedirectResponse
    {
        $pengurus->delete();

        return back()->with('sukses', 'Pengurus dihapus dari daftar.');
    }
}
