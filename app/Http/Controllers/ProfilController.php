<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Edit profil milik pengguna yang sedang masuk: data diri, avatar, dan sandi.
 *
 * Email, NIS, kelas, dan peran sengaja tidak dapat diubah dari sini —
 * ketiganya data identitas yang harus lewat pengelola.
 */
class ProfilController extends Controller
{
    /** Form edit profil. */
    public function edit(Request $request): View
    {
        $user = $request->user()->loadMissing('kelas.jurusan');

        return view('profil-edit', compact('user'));
    }

    /** Perbarui nama, bio, telepon, dan avatar. */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'bio.max' => 'Bio maksimal 255 karakter.',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'avatar.image' => 'Berkas harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        // Avatar baru: hapus berkas lama supaya tidak menumpuk di storage
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatar', 'public');
        }

        $user->update($validated);

        return back()->with('sukses', 'Profil berhasil diperbarui.');
    }

    /** Ganti kata sandi — wajib menyertakan sandi lama. */
    public function ubahSandi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sandi_lama' => ['required', 'current_password'],
            'sandi' => ['required', 'confirmed', Password::min(8)],
        ], [
            'sandi_lama.required' => 'Kata sandi sekarang wajib diisi.',
            'sandi_lama.current_password' => 'Kata sandi sekarang tidak cocok.',
            'sandi.required' => 'Kata sandi baru wajib diisi.',
            'sandi.confirmed' => 'Ulangi kata sandi baru dengan sama.',
            'sandi.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $request->user()->update(['password' => $validated['sandi']]);

        return back()->with('sukses', 'Kata sandi berhasil diganti.');
    }
}
