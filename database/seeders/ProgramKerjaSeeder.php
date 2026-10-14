<?php

namespace Database\Seeders;

use App\Models\ProgramKerja;
use Illuminate\Database\Seeder;

class ProgramKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Pelantikan & Pembinaan Pengurus Baru',
                'bidang' => 'Pengurus Harian',
                'status' => 'selesai',
                'progres' => 100,
                'penanggung_jawab' => 'Ketua OSIS',
                'deskripsi' => "Masa orientasi pengurus baru: pengenalan struktur OSIS, tata tertib organisasi, serta pembagian tugas tiap bidang agar program tahunan berjalan tertib.\n\nKegiatan mencakup rangkaian diklat internal selama dua pekan.",
            ],
            [
                'nama' => 'Class Meeting Antar Kelas',
                'bidang' => 'Olahraga & Seni',
                'status' => 'berjalan',
                'progres' => 45,
                'penanggung_jawab' => 'Koordinator Olahraga & Seni',
                'deskripsi' => "Pekan olahraga & seni antar kelas dalam rangka memeriahkan kegiatan pelajar. Cabang: futsal, basket, voli, badminton, catur, serta pentas seni kelas.\n\nJadwal pertandingan mengikuti kalender akademik semester ini.",
            ],
            [
                'nama' => 'Pentas Seni Neper',
                'bidang' => 'Olahraga & Seni',
                'status' => 'rencana',
                'progres' => 10,
                'penanggung_jawab' => 'Koordinator Olahraga & Seni',
                'deskripsi' => "Pementasan seni akbar warga sekolah: band, tari, teater, dan fashion show busana daur ulang sebagai wujud kreativitas siswa.",
            ],
            [
                'nama' => 'Lomba Kompetensi Siswa (LKS)',
                'bidang' => 'Akademik',
                'status' => 'berjalan',
                'progres' => 60,
                'penanggung_jawab' => 'Koordinator Prestasi Akademik',
                'deskripsi' => "Pembinaan siswa menuju LKS tingkat kabupaten dan provinsi pada bidang lomba: pemrograman web, jaringan, desain, dan otomotif.",
            ],
            [
                'nama' => 'Bulan Bahasa & Literasi Sekolah',
                'bidang' => 'Akademik',
                'status' => 'rencana',
                'progres' => 0,
                'penanggung_jawab' => 'Koordinator Akademik',
                'deskripsi' => "Rangkaian kegiatan membaca, menulis karya, dan pidato Bahasa Indonesia serta Bahasa Inggris untuk seluruh warga sekolah.",
            ],
            [
                'nama' => 'Aksi Sosial & Peduli Lingkungan',
                'bidang' => 'Sosial',
                'status' => 'rencana',
                'progres' => 5,
                'penanggung_jawab' => 'Koordinator Sosial & Lingkungan',
                'deskripsi' => "Bakti sosial ke panti asuhan, donor darah, kerja bakti lingkungan sekolah, dan kampanye zero waste.",
            ],
            [
                'nama' => 'Seminar Karier & Kunjungan Industri',
                'bidang' => 'Kegiatan',
                'status' => 'rencana',
                'progres' => 15,
                'penanggung_jawab' => 'Koordinator Kegiatan',
                'deskripsi' => "Seminar wirausaha & karier bersama alumni, serta kunjungan industri ke mitra perusahaan di sekitar Cirebon.",
            ],
            [
                'nama' => 'Peringatan Hari Besar Nasional',
                'bidang' => 'Kegiatan',
                'status' => 'rencana',
                'progres' => 0,
                'penanggung_jawab' => 'Pengurus Harian',
                'deskripsi' => "Upacara dan lomba antar kelas memperingati hari besar nasional seperti Hardiknas, Sumpah Pemuda, dan Hari Pendidikan Nasional.",
            ],
        ];

        foreach ($data as $i => $d) {
            ProgramKerja::updateOrCreate(
                ['nama' => $d['nama']],
                [
                    'slug' => \Illuminate\Support\Str::slug($d['nama']),
                    'bidang' => $d['bidang'],
                    'status' => $d['status'],
                    'progres' => $d['progres'],
                    'penanggung_jawab' => $d['penanggung_jawab'],
                    'deskripsi' => $d['deskripsi'],
                    'tanggal_mulai' => now()->addMonths(-1)->addDays($i * 6),
                    'tanggal_selesai' => now()->addMonths(3),
                    'user_id' => \App\Models\User::where('role', 'admin')->value('id'),
                ]
            );
        }
    }
}
