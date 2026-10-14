<?php

namespace App\Services;

use App\Models\Topik;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Menghitung & menyimpan daftar Trending Topic.
 * Skor = jumlah post (1/posting) + komentar + suka dalam 7 hari terakhir.
 */
class TrendingTopicService
{
    /** Cache skor selama 2 menit agar tetap segar tapi ringan. */
    private const CACHE_TTL = 120;

    public function daftar(int $limit = 8): Collection
    {
        try {
            return Cache::remember(
                'osis.trending-topik',
                self::CACHE_TTL,
                fn () => Topik::trending($limit)->get()
            );
        } catch (\Throwable $e) {
            // Gagal baca/tulis cache jangan membuat halaman error
            report($e);

            return Topik::trending($limit)->get();
        }
    }

    public function lupakanCache(): void
    {
        Cache::forget('osis.trending-topik');
    }
}
