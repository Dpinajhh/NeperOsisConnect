<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = \App\Models\Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->siswa(),
            'konten' => fake()->realText(mt_rand(40, 220)),
            'gambar' => null,
            'visibilitas' => 'sekolah',
            'jumlah_suka' => fake()->numberBetween(0, 40),
            'jumlah_komentar' => 0,
        ];
    }

    public function denganGambar(): static
    {
        return $this->state(fn () => ['gambar' => 'posts/contoh.jpg']);
    }

    public function visibilitas(string $v): static
    {
        return $this->state(fn () => ['visibilitas' => $v]);
    }
}
