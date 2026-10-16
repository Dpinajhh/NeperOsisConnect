<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Password semua akun uji: "password"
     */
    public function run(): void
    {
        // Peran, status, & verifikasi email tidak fillable (lihat App\Models\User),
        // jadi ditetapkan eksplisit lewat forceFill.
        $akun = function (string $email, array $data, string $role): User {
            $user = User::updateOrCreate(['email' => $email], $data);

            $user->forceFill([
                'role' => $role,
                'is_active' => true,
                'email_verified_at' => now(),
            ])->save();

            return $user;
        };

        // ─── Akun uji utama ───────────────────────────────
        $akun('admin@osisconnect.test', [
            'name' => 'Ketua OSIS',
            'nis' => 'ADM001',
            'password' => 'password',   // otomatis di-hash oleh cast 'hashed'
            'bio' => 'Pengelola platform Osis Connect.',
        ], 'admin');

        $akun('guru@osisconnect.test', [
            'name' => 'Pembina OSIS',
            'nis' => 'GR001',
            'password' => 'password',
            'bio' => 'Pembina kegiatan siswa.',
        ], 'guru');

        $siswaUji = $akun('siswa@osisconnect.test', [
            'name' => 'Siswa Uji',
            'nis' => 'SW001',
            'password' => 'password',
            'bio' => 'Siswa aktif dan suka berbagi cerita.',
        ], 'siswa');

        if (! $siswaUji->kelas_id) {
            $siswaUji->update(['kelas_id' => \App\Models\Kelas::where('nama', 'like', 'X PPLG%')->first()?->id]);
        }

        // ─── Tambahan user random ─────────────────────────
        // 1 admin + 2 guru ekstra
        User::factory()->admin()->count(1)->create();
        User::factory()->guru()->count(2)->create();

        // 40 siswa
        User::factory()->siswa()->count(40)->create();
    }
}
