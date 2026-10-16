<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    /**
     * Daftar semua pengguna + filter & pencarian.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->string('q'));
        $filter = $request->string('filter')->value() ?: 'semua';

        $pengguna = User::query()
            ->with('kelas.jurusan')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('nis', 'like', "%{$q}%");
                });
            })
            ->when($filter === 'aktif', fn ($query) => $query->where('is_active', true)->where('is_banned', false))
            ->when($filter === 'nonaktif', fn ($query) => $query->where('is_active', false))
            ->when($filter === 'diblokir', fn ($query) => $query->where('is_banned', true))
            ->when($filter !== 'semua' && in_array($filter, ['siswa', 'guru', 'admin'], true), fn ($query) => $query->where('role', $filter))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $ringkas = [
            'semua' => User::count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'diblokir' => User::where('is_banned', true)->count(),
        ];

        return view('admin.pengguna', compact('pengguna', 'ringkas', 'q', 'filter'));
    }

    /**
     * Ubah peran pengguna (admin only).
     */
    public function ubahPeran(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,guru,siswa'],
        ], [
            'role.in' => 'Peran tidak valid.',
        ]);

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Kamu tidak bisa mengubah peran akunmu sendiri.');
        }

        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            // Pastikan selalu ada minimal satu admin lain
            $adminLain = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($adminLain === 0) {
                return back()->with('error', 'Tidak bisa menurunkan admin terakhir. Tunjuk admin lain dulu.');
            }
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('sukses', "Peran {$user->name} diubah menjadi {$validated['role']}.");
    }

    /**
     * Aktif / nonaktifkan akun.
     */
    public function statusAkun(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Kamu tidak bisa menonaktifkan akunmu sendiri.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('sukses', $user->is_active
            ? "Akun {$user->name} berhasil diaktifkan."
            : "Akun {$user->name} dinonaktifkan.");
    }

    /**
     * Blokir / lepas blokir akun.
     */
    public function blokir(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Kamu tidak bisa memblokir akunmu sendiri.');
        }

        $user->update([
            'is_banned' => ! $user->is_banned,
            'is_active' => $user->is_banned, // saat dilepas blokir, aktifkan kembali
        ]);

        return back()->with('sukses', $user->is_banned
            ? "Akun {$user->name} berhasil diblokir."
            : "Blokir untuk {$user->name} dilepas.");
    }
}
