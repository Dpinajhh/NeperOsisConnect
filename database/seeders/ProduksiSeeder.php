<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder aman untuk instalasi produksi.
 *
 * Hanya mengisi data acuan (jurusan, kelas, kategori, topik) dan - bila
 * ADMIN_EMAIL & ADMIN_PASSWORD diisi di .env - satu akun admin. Tidak
 * ada akun uji berpassword "password" maupun pengguna random seperti
 * pada UserSeeder yang dipakai di lingkungan pengembangan.
 *
 * Jalankan ulang kapan saja tanpa menggandakan data (semua seeder
 * memakai updateOrCreate).
 */
class ProduksiSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JurusanSeeder::class,
            KelasSeeder::class,
            KategoriSeeder::class,
            TopikSeeder::class,
        ]);

        $email = config('osis.admin.email');
        $sandi = config('osis.admin.password');

        if (blank($email) || blank($sandi)) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD belum diisi: akun admin dilewati.');
            $this->command?->line('Isi keduanya di .env lalu ulangi: php artisan db:seed --class=ProduksiSeeder --force');

            return;
        }

        if (mb_strlen((string) $sandi) < 8) {
            $this->command?->error('ADMIN_PASSWORD minimal 8 karakter: akun admin tidak dibuat.');

            return;
        }

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('osis.admin.name') ?: 'Administrator',
                'password' => $sandi, // di-hash otomatis oleh cast 'hashed'
            ]
        );

        // Peran & status tidak fillable (lihat App\Models\User)
        $admin->forceFill([
            'role' => 'admin',
            'is_active' => true,
            'is_banned' => false,
            'email_verified_at' => now(),
        ])->save();

        $this->command?->info("Akun admin siap: {$email}");
        $this->command?->line('Jangan lupa hapus ADMIN_PASSWORD dari .env setelah ini.');
    }
}
