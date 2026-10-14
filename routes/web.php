<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HalamanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PollingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfilOsisController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\TopikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Osis Connect
|--------------------------------------------------------------------------
| Platform siswa SMK Negeri 1 Cirebon.
| Grup: publik → tamu (guest) → terautentikasi (auth + aktif).
*/

// ─── Publik ──────────────────────────────────────
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

// Profil OSIS — terbuka untuk semua (termasuk tamu)
Route::get('/osis', [ProfilOsisController::class, 'index'])->name('profil-osis');

// Program kerja & Event — terbuka untuk semua (termasuk tamu)
Route::get('/program-kerja', [ProgramKerjaController::class, 'index'])->name('program-kerja');
Route::get('/program-kerja/{program}', [ProgramKerjaController::class, 'show'])->name('program-kerja.detail');

Route::get('/event', [EventController::class, 'index'])->name('event');
Route::get('/event/{event}', [EventController::class, 'show'])->name('event.detail');

// Polling — daftar & detail terbuka; memilih perlu login
Route::get('/polling', [PollingController::class, 'index'])->name('polling');
Route::get('/polling/{polling}', [PollingController::class, 'show'])->name('polling.detail');

// ─── Tamu (belum login) ─────────────────────────
Route::middleware('guest')->group(function (): void {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->name('login.store');

    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/lupa-sandi', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/lupa-sandi', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-sandi/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-sandi', [NewPasswordController::class, 'store'])->name('password.store');
});

// ─── Keluar (harus sudah login) ──────────────────
Route::post('/keluar', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ─── Siswa & pengelola (sudah login + akun aktif) ─
Route::middleware(['auth', 'aktif'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Feed sosial (gaya Instagram/TikTok) ──────────
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');

    // Program kerja & Event — pendaftaran peserta (perlu login)
    Route::post('/event/{event}/daftar', [EventController::class, 'daftar'])->name('event.daftar');
    Route::delete('/event/{event}/batal', [EventController::class, 'batal'])->name('event.batal');

    // Polling — memberi suara (perlu login)
    Route::post('/polling/{polling}/suara', [PollingController::class, 'vote'])->name('polling.vote');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/suka', [PostController::class, 'suka'])->name('posts.suka');
    Route::post('/posts/{post}/komentar', [PostController::class, 'komentar'])->name('posts.komentar');
    Route::delete('/posts/{post}/komentar/{komentar}', [PostController::class, 'hapusKomentar'])->name('posts.komentar.hapus');

    // Topik / hashtag (Trending Topic)
    Route::get('/topik/{topik:slug}', [TopikController::class, 'show'])->name('topik.show');

    // Halaman inti — fitur lengkap dibangun bertahap
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.detail');

    Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/{berita}', [BeritaController::class, 'show'])->name('berita.detail');
    Route::get('/notifikasi', [HalamanController::class, 'notifikasi'])->name('notifikasi.index');

    // Profil
    Route::get('/profil', [HalamanController::class, 'profil'])->name('profil.edit');
    Route::get('/pengguna/{user}', [HalamanController::class, 'profilPublik'])->name('profil.show');

    // Panel pengelola — hanya admin & guru
    Route::middleware('role:admin,guru')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', [HalamanController::class, 'kelola'])->name('beranda');

        // Profil OSIS & kepengurusan
        Route::get('/profil-osis', [ProfilOsisController::class, 'edit'])->name('profil-osis.edit');
        Route::put('/profil-osis', [ProfilOsisController::class, 'update'])->name('profil-osis.update');
        Route::post('/kepengurusan', [ProfilOsisController::class, 'simpanPengurus'])->name('kepengurusan.store');
        Route::delete('/kepengurusan/{pengurus}', [ProfilOsisController::class, 'hapusPengurus'])->name('kepengurusan.hapus');

        // Pengumuman
        Route::get('/pengumuman', [PengumumanController::class, 'kelola'])->name('pengumuman.kelola');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::put('/pengumuman/{pengumuman}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/pengumuman/{pengumuman}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
        Route::post('/pengumuman/{pengumuman}/sematkan', [PengumumanController::class, 'sematkan'])->name('pengumuman.sematkan');

        // Berita
        Route::get('/berita', [BeritaController::class, 'kelola'])->name('berita.kelola');
        Route::post('/berita', [BeritaController::class, 'store'])->name('berita.store');
        Route::put('/berita/{berita}', [BeritaController::class, 'update'])->name('berita.update');
        Route::delete('/berita/{berita}', [BeritaController::class, 'destroy'])->name('berita.destroy');

        // Program kerja
        Route::get('/program-kerja', [ProgramKerjaController::class, 'kelola'])->name('program-kerja.kelola');
        Route::post('/program-kerja', [ProgramKerjaController::class, 'store'])->name('program-kerja.store');
        Route::put('/program-kerja/{program}', [ProgramKerjaController::class, 'update'])->name('program-kerja.update');
        Route::delete('/program-kerja/{program}', [ProgramKerjaController::class, 'destroy'])->name('program-kerja.destroy');

        // Event / kegiatan
        Route::get('/event', [EventController::class, 'kelola'])->name('event.kelola');
        Route::post('/event', [EventController::class, 'store'])->name('event.store');
        Route::put('/event/{event}', [EventController::class, 'update'])->name('event.update');
        Route::delete('/event/{event}', [EventController::class, 'destroy'])->name('event.destroy');

        // Polling
        Route::get('/polling', [PollingController::class, 'kelola'])->name('polling.kelola');
        Route::post('/polling', [PollingController::class, 'store'])->name('polling.store');
        Route::put('/polling/{polling}', [PollingController::class, 'update'])->name('polling.update');
        Route::delete('/polling/{polling}', [PollingController::class, 'destroy'])->name('polling.destroy');
        Route::post('/polling/{polling}/tutup', [PollingController::class, 'tutup'])->name('polling.tutup');
    });
});
