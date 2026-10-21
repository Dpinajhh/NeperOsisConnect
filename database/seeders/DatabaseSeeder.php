<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting: jurusan → kelas → user → konten
     *
     * Di produksi (APP_ENV=production) hanya ProduksiSeeder yang jalan:
     * data acuan + akun admin dari .env, tanpa akun uji/demo.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->call(ProduksiSeeder::class);

            return;
        }

        $this->call([
            JurusanSeeder::class,
            KelasSeeder::class,
            KategoriSeeder::class,
            UserSeeder::class,
            TopikSeeder::class,
            PostSeeder::class,
            ProfilOsisSeeder::class,
            ProgramKerjaSeeder::class,
            EventSeeder::class,
            PollingSeeder::class,
            PengumumanSeeder::class,
            BeritaSeeder::class,
        ]);
    }
}
