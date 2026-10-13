<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notifikasi>
 */
class NotifikasiFactory extends Factory
{
    protected $model = \App\Models\Notifikasi::class;

    public function definition(): array
    {
        $tipe = fake()->randomElement(['suka', 'komentar', 'pengumuman', 'berita']);

        return [
            'user_id' => User::factory()->siswa(),
            'tipe' => $tipe,
            'judul' => $this->judul($tipe),
            'pesan' => fake()->sentence(8),
            'url' => '/feed',
            'dibaca_at' => null,
        ];
    }

    private function judul(string $tipe): string
    {
        return match ($tipe) {
            'suka' => 'Seseorang menyukai postinganmu',
            'komentar' => 'Ada komentar baru di postinganmu',
            'pengumuman' => 'Pengumuman baru dari OSIS',
            'berita' => 'Berita sekolah terbaru',
            default => 'Notifikasi',
        };
    }
}
