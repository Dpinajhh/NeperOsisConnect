<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Jurusan yang umum ada di SMK.
     */
    private const DAFTAR = [
        ['PPLG', 'Pengembangan Perangkat Lunak dan Gim'],
        ['TKJ', 'Teknik Komputer dan Jaringan'],
        ['DKV', 'Desain Komunikasi Visual'],
        ['TBSM', 'Teknik dan Bisnis Sepeda Motor'],
        ['MPLB', 'Manajemen Perkantoran dan Layanan Bisnis'],
        ['AKL', 'Akuntansi dan Keuangan Lembaga'],
    ];

    public function run(): void
    {
        foreach (self::DAFTAR as [$kode, $nama]) {
            Jurusan::updateOrCreate(
                ['kode' => $kode],
                ['nama' => $nama, 'keterangan' => 'Jurusan '.$kode]
            );
        }
    }
}
