<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // URL yang dibuat aplikasi (termasuk tautan reset sandi di email)
        // selalu memakai APP_URL, bukan header Host dari permintaan. Tanpa
        // ini, penyerang bisa mengirim Host palsu dan membuat tautan reset
        // mengarah ke domain mereka.
        URL::forceRootUrl(rtrim((string) config('app.url'), '/'));

        $this->daftarkanPembatasLaju();
    }

    /**
     * Pembatas laju per aksi.
     *
     * Bentuk `throttle:20,60` bawaan Laravel memakai kunci yang sama untuk
     * SEMUA rute (hanya berdasarkan ID pengguna atau IP). Akibatnya jatah
     * satu aksi ikut menghabiskan aksi lain: misalnya beberapa komentar
     * membuat tombol voting ikut terkena 429. Limiter bernama di bawah
     * memakai kunci unik per aksi sehingga jatahnya terpisah.
     */
    private function daftarkanPembatasLaju(): void
    {
        $kunci = fn (Request $request, string $aksi): string => $aksi.'|'.($request->user()?->id ?? $request->ip());

        RateLimiter::for('daftar', fn (Request $request) => Limit::perHour(5)->by('daftar|'.$request->ip()));
        RateLimiter::for('lupa-sandi', fn (Request $request) => Limit::perHour(5)->by('lupa-sandi|'.$request->ip()));

        RateLimiter::for('lapor', fn (Request $request) => Limit::perHour(10)->by($kunci($request, 'lapor')));
        RateLimiter::for('posts', fn (Request $request) => Limit::perHour(20)->by($kunci($request, 'posts')));
        RateLimiter::for('posts-ubah', fn (Request $request) => Limit::perHour(30)->by($kunci($request, 'posts-ubah')));
        RateLimiter::for('suka', fn (Request $request) => Limit::perMinute(120)->by($kunci($request, 'suka')));
        RateLimiter::for('komentar', fn (Request $request) => Limit::perMinute(30)->by($kunci($request, 'komentar')));
        RateLimiter::for('notif-baca', fn (Request $request) => Limit::perMinute(60)->by($kunci($request, 'notif-baca')));
        RateLimiter::for('notif-semua', fn (Request $request) => Limit::perMinute(30)->by($kunci($request, 'notif-semua')));
        RateLimiter::for('polling', fn (Request $request) => Limit::perMinute(10)->by($kunci($request, 'polling')));
        RateLimiter::for('event', fn (Request $request) => Limit::perMinute(20)->by($kunci($request, 'event')));
    }
}
