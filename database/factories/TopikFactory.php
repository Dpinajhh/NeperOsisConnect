<?php

namespace Database\Factories;

use App\Models\Topik;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Topik>
 */
class TopikFactory extends Factory
{
    protected $model = Topik::class;

    public function definition(): array
    {
        $nama = Str::lower(fake()->unique()->word());

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'jumlah_post' => 0,
        ];
    }
}
