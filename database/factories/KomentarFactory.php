<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Komentar>
 */
class KomentarFactory extends Factory
{
    protected $model = \App\Models\Komentar::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->siswa(),
            'post_id' => Post::factory(),
            'parent_id' => null,
            'konten' => fake()->realText(mt_rand(15, 120)),
        ];
    }

    public function balasan(int $parentId): static
    {
        return $this->state(fn () => ['parent_id' => $parentId]);
    }
}
