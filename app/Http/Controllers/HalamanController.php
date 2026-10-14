<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

/**
 * Halaman simpul (placeholder) untuk fitur yang rutenya sudah
 * dipakai navbar. Konten lengkap tiap modul dibangun bertahap
 * pada fase berikutnya (feed → pengumuman → berita → …).
 */
class HalamanController extends Controller
{
    /** Feed: Fase berikutnya */
    public function feed(): View
    {
        return view('halaman.umum', [
            'ikon' => '💬',
            'judul' => 'Feed Siswa',
            'deskripsi' => 'Tempat berbagi cerita, kegiatan, dan pendapat antarwarga sekolah. Segera hadir — kamu akan bisa posting, menyukai, dan mengomentari.',
        ]);
    }

    /** Pengumuman: Fase berikutnya */
    public function pengumuman(): View
    {
        return view('halaman.umum', [
            'ikon' => '📢',
            'judul' => 'Pengumuman',
            'deskripsi' => 'Informasi resmi dari pengurus OSIS dan guru — bisa untuk seluruh sekolah, satu jurusan, atau satu kelas.',
        ]);
    }

    /** Berita: Fase berikutnya */
    public function berita(): View
    {
        return view('halaman.umum', [
            'ikon' => '📰',
            'judul' => 'Berita Sekolah',
            'deskripsi' => 'Kegiatan, prestasi, dan liputan kegiatan SMK Negeri 1 Cirebon. Segera hadir.',
        ]);
    }

    /** Notifikasi: Fase berikutnya */
    public function notifikasi(): View
    {
        $jumlahBelumDibaca = auth()->user()->notifikasi()->belumDibaca()->count();

        return view('halaman.umum', [
            'ikon' => '🔔',
            'judul' => 'Notifikasi',
            'deskripsi' => $jumlahBelumDibaca > 0
                ? "Ada {$jumlahBelumDibaca} notifikasi yang belum dibaca."
                : 'Notifikasi suka, komentar, dan pengumuman baru akan muncul di sini. Segera hadir.',
        ]);
    }

    /** Form edit profil: Fase berikutnya */
    public function profil(): View
    {
        return view('halaman.umum', [
            'ikon' => '✏️',
            'judul' => 'Profil Saya',
            'deskripsi' => 'Pengaturan nama, avatar, bio, dan data akun akan bisa diubah dari halaman ini. Segera hadir.',
        ]);
    }

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
