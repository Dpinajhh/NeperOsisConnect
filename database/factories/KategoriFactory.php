<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategori>
 */
class KategoriFactory extends Factory
{
    protected $model = \App\Models\Kategori::class;

    public function definition(): array
    {
        $nama = fake()->randomElement([
            'Kegiatan Sekolah', 'Ekstrakurikuler', 'Prestasi', 'Akademik',
            'Organisasi', 'Teknologi', 'Sosial', 'Layanan Siswa',
        ]);

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'tipe' => fake()->randomElement(['pengumuman', 'berita']),
            'warna' => fake()->randomElement(['slate', 'indigo', 'emerald', 'amber', 'rose', 'sky']),
        ];
    }
}
