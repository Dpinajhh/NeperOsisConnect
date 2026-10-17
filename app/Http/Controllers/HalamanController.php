<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

/**
 * Halaman umum yang tidak butuh controller sendiri:
 * profil publik pengguna dan beranda panel pengelola.
 */
class HalamanController extends Controller
{
    /** Halaman profil publik seorang pengguna */
    public function profilPublik(User $user): View
    {
        $user->loadMissing('kelas.jurusan');

        return view('halaman.profil', compact('user'));
    }

    /** Beranda panel pengelola (admin & guru) */
    public function kelola(): View
    {
        return view('halaman.kelola');
    }
}
