<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Berita>
 */
class BeritaFactory extends Factory
{
    protected $model = \App\Models\Berita::class;

    public function definition(): array
    {
        $judul = fake()->unique()->sentence(6);

        return [
            'user_id' => User::factory()->guru(),
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'ringkasan' => fake()->paragraph(2),
            'konten' => implode("\n\n", [
                fake()->paragraph(4),
                fake()->paragraph(4),
                fake()->paragraph(3),
                fake()->paragraph(2),
            ]),
            'cover' => null,
            'status' => 'published',
            'is_headline' => false,
            'published_at' => now()->subDays(mt_rand(0, 30)),
            'dilihat' => fake()->numberBetween(0, 900),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function headline(): static
    {
        return $this->state(fn () => ['is_headline' => true]);
    }
}
