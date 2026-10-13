<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    protected $model = \App\Models\Kelas::class;

    public function definition(): array
    {
        $tingkat = fake()->randomElement(['X', 'XI', 'XII']);

        return [
            'nama' => $tingkat.' '.strtoupper(fake()->unique()->lexify('???')).' '.fake()->numberBetween(1, 3),
            'tingkat' => $tingkat,
            'jurusan_id' => Jurusan::factory(),
        ];
    }
}
