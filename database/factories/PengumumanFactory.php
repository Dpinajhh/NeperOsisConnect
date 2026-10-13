<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengumuman>
 */
class PengumumanFactory extends Factory
{
    protected $model = \App\Models\Pengumuman::class;

    public function definition(): array
    {
        $judul = fake()->unique()->sentence(5);

        return [
            'user_id' => User::factory()->admin(),
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'konten' => implode("\n\n", [
                fake()->paragraph(4),
                fake()->paragraph(3),
                fake()->paragraph(2),
            ]),
            'cover' => null,
            'target' => 'semua',
            'kelas_id' => null,
            'jurusan_id' => null,
            'is_pinned' => false,
            'published_at' => now()->subDays(mt_rand(0, 30)),
            'dilihat' => fake()->numberBetween(0, 500),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn () => ['is_pinned' => true]);
    }

    public function target(string $t, ?int $id = null): static
    {
        return $this->state(fn () => [
            'target' => $t,
            'kelas_id' => $t === 'kelas' ? $id : null,
            'jurusan_id' => $t === 'jurusan' ? $id : null,
        ]);
    }
}
