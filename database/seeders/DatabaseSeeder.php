<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting: jurusan → kelas → user → konten
     */
    public function run(): void
    {
        $this->call([
            JurusanSeeder::class,
            KelasSeeder::class,
            KategoriSeeder::class,
            UserSeeder::class,
            TopikSeeder::class,
            PostSeeder::class,
            ProfilOsisSeeder::class,
            PengumumanSeeder::class,
            BeritaSeeder::class,
        ]);
    }
}
