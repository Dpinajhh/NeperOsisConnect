<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Buat kelas untuk setiap jurusan: X, XI, XII masing-masing 2 kelas.
     */
    public function run(): void
    {
        $tingkat = ['X', 'XI', 'XII'];

        foreach (Jurusan::all() as $jurusan) {
            foreach ($tingkat as $level) {
                for ($i = 1; $i <= 2; $i++) {
                    Kelas::updateOrCreate(
                        [
                            'nama' => $level.' '.$jurusan->kode.' '.$i,
                            'jurusan_id' => $jurusan->id,
                        ],
                        ['tingkat' => $level]
                    );
                }
            }
        }
    }
}
