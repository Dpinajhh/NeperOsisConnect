<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HalamanController;
use App\Http\Controllers\PostController;
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
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/suka', [PostController::class, 'suka'])->name('posts.suka');
    Route::post('/posts/{post}/komentar', [PostController::class, 'komentar'])->name('posts.komentar');
    Route::delete('/posts/{post}/komentar/{komentar}', [PostController::class, 'hapusKomentar'])->name('posts.komentar.hapus');

    // Topik / hashtag (Trending Topic)
    Route::get('/topik/{topik:slug}', [TopikController::class, 'show'])->name('topik.show');

    // Halaman inti — fitur lengkap dibangun bertahap
    Route::get('/pengumuman', [HalamanController::class, 'pengumuman'])->name('pengumuman.index');
    Route::get('/berita', [HalamanController::class, 'berita'])->name('berita.index');
    Route::get('/notifikasi', [HalamanController::class, 'notifikasi'])->name('notifikasi.index');

    // Profil
    Route::get('/profil', [HalamanController::class, 'profil'])->name('profil.edit');
    Route::get('/pengguna/{user}', [HalamanController::class, 'profilPublik'])->name('profil.show');

    // Panel pengelola — hanya admin & guru
    Route::middleware('role:admin,guru')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', [HalamanController::class, 'kelola'])->name('beranda');
    });
});
