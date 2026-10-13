<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Laporan>
 */
class LaporanFactory extends Factory
{
    protected $model = \App\Models\Laporan::class;

    public function definition(): array
    {
        return [
            'pelapor_id' => User::factory()->siswa(),
            'laporanable_type' => Post::class,
            'laporanable_id' => Post::factory(),
            'alasan' => fake()->randomElement([
                'Konten tidak pantas / SARA',
                'Bullying atau mengejek teman',
                'Spam atau promosi',
                'Informasi palsu (hoaks)',
                'Pelanggaran privasi siswa lain',
                'Konten kekerasan',
            ]),
            'status' => 'open',
            'catatan_admin' => null,
            'ditangani_oleh' => null,
        ];
    }
}
