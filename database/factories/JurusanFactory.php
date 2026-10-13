<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jurusan>
 */
class JurusanFactory extends Factory
{
    protected $model = \App\Models\Jurusan::class;

    public function definition(): array
    {
        $kode = strtoupper(fake()->unique()->lexify('??'));

        return [
            'kode' => $kode,
            'nama' => $this->namaJurusan($kode),
            'keterangan' => fake()->sentence(6),
        ];
    }

    private function namaJurusan(string $kode): string
    {
        $map = [
            'PPLG' => 'Pengembangan Perangkat Lunak dan Gim',
            'TKJ' => 'Teknik Komputer dan Jaringan',
            'DKV' => 'Desain Komunikasi Visual',
            'TBSM' => 'Teknik dan Bisnis Sepeda Motor',
            'MPLB' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'AKL' => 'Akuntansi dan Keuangan Lembaga',
            'PM' => 'Pemasaran',
            'ANIM' => 'Animasi',
            'BC' => 'Broadcasting',
            'KUL' => 'Kuliner',
        ];

        return $map[$kode] ?? 'Jurusan '.$kode;
    }
}
