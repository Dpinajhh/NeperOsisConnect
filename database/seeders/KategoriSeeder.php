<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    private const PENGUMUMAN = [
        'Kegiatan Sekolah', 'Ekstrakurikuler', 'Prestasi', 'Organisasi', 'Layanan Siswa',
    ];

    private const BERITA = [
        'Akademik', 'Teknologi', 'Sosial', 'Olahraga', 'Seni & Budaya',
    ];

    public function run(): void
    {
        foreach (self::PENGUMUMAN as $nama) {
            Kategori::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama, 'tipe' => 'pengumuman', 'warna' => 'indigo']
            );
        }

        foreach (self::BERITA as $nama) {
            Kategori::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama, 'tipe' => 'berita', 'warna' => 'emerald']
            );
        }
    }
}
