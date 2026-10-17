<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
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

        $penglihat = auth()->user();

        // Pemilik & pengelola melihat seluruh aktivitas; pengunjung lain
        // hanya menghitung yang boleh dilihat sesuai visibilitas postingan.
        $lihatSemua = $penglihat && ($penglihat->id === $user->id || $penglihat->isPengelola());

        $jumlahPost = $lihatSemua
            ? $user->posts()->count()
            : $user->posts()->terlihatUntuk($penglihat)->count();

        $jumlahKomentar = Komentar::query()
            ->where('user_id', $user->id)
            ->when(! $lihatSemua, fn ($q) => $q->whereHas('post', fn ($p) => $p->terlihatUntuk($penglihat)))
            ->count();

        return view('halaman.profil', compact('user', 'jumlahPost', 'jumlahKomentar'));
    }

    /** Beranda panel pengelola (admin & guru) */
    public function kelola(): View
    {
        return view('halaman.kelola');
    }
}
