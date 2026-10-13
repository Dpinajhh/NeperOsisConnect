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
        // ─── Akun uji utama ───────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@osisconnect.test'],
            [
                'name' => 'Ketua OSIS',
                'nis' => 'ADM001',
                'role' => 'admin',
                'password' => 'password',   // otomatis di-hash oleh cast 'hashed'
                'bio' => 'Pengelola platform Osis Connect.',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'guru@osisconnect.test'],
            [
                'name' => 'Pembina OSIS',
                'nis' => 'GR001',
                'role' => 'guru',
                'password' => 'password',
                'bio' => 'Pembina kegiatan siswa.',
                'email_verified_at' => now(),
            ]
        );

        $siswaUji = User::updateOrCreate(
            ['email' => 'siswa@osisconnect.test'],
            [
                'name' => 'Siswa Uji',
                'nis' => 'SW001',
                'role' => 'siswa',
                'password' => 'password',
                'bio' => 'Siswa aktif dan suka berbagi cerita.',
                'email_verified_at' => now(),
            ]
        );

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
