<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),

            // === Osis Connect ===
            'nis' => fake()->unique()->numerify('####'),
            'role' => 'siswa',
            'kelas_id' => null,
            'avatar' => null,
            'bio' => fake()->randomElement([null, fake()->sentence(8)]),
            'telepon' => fake()->numerify('08##########'),
            'is_active' => true,
            'is_banned' => false,
            'last_login_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    // ─── State role ────────────────────────────────────────────

    public function siswa(): static
    {
        return $this->state(function () {
            $kelas = Kelas::inRandomOrder()->first() ?? Kelas::factory()->create();

            return [
                'role' => 'siswa',
                'kelas_id' => $kelas->id,
            ];
        });
    }

    public function guru(): static
    {
        return $this->state(fn () => ['role' => 'guru', 'kelas_id' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin', 'kelas_id' => null]);
    }

    public function banned(): static
    {
        return $this->state(fn () => ['is_banned' => true]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    /**
     * Akun bernama untuk login testing.
     */
    public function akunUji(string $role = 'siswa'): static
    {
        return $this->state(fn () => [
            'role' => $role,
            'name' => ucfirst($role).' Uji',
            'email' => $role.'@osisconnect.test',
            'nis' => strtoupper(substr($role, 0, 3)).'001',
            'last_login_at' => now(),
        ]);
    }
}
