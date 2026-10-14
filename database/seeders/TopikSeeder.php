<?php

namespace Database\Seeders;

use App\Models\Topik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TopikSeeder extends Seeder
{
    /**
     * Topik/hashtag yang sering dipakai warga SMK Negeri 1 Cirebon.
     */
    private const DAFTAR = [
        'classmeeting', 'osis', 'neper', 'p5', 'lks', 'upacara', 'pramuka', 'paskibra',
        'livecoding', 'pentasseni', 'hardiknas', 'bulanbahasa', 'kerjabakti', 'pkl',
        'seminar', 'lomba', 'kelasxii', 'ekstrakurikuler', 'kunjunganindustri', 'wisudaneper',
    ];

    public function run(): void
    {
        foreach (self::DAFTAR as $nama) {
            Topik::updateOrCreate(
                ['nama' => $nama],
                ['slug' => Str::slug($nama)]
            );
        }
    }
}
