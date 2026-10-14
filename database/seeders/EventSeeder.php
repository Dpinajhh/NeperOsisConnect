<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            return;
        }

        $siswa = User::where('role', 'siswa')->pluck('id')->all();
        $classMeeting = ProgramKerja::where('nama', 'like', 'Class Meeting%')->value('id');
        $pentas = ProgramKerja::where('nama', 'like', 'Pentas Seni%')->value('id');
        $seminar = ProgramKerja::where('nama', 'like', 'Seminar Karier%')->value('id');

        $data = [
            [
                'nama' => 'Futsal Class Meeting 2026',
                'deskripsi' => "Laga futsal antar kelas mewakili tiap jurusan. Pendaftaran tim melalui masing-masing ketua kelas.\n\nHadiah: piala bergilir, medali, dan sertifikat juara.",
                'lokasi' => 'Lapangan Serbaguna Sekolah',
                'waktu' => now()->addDays(7),
                'kuota' => 120,
                'program_kerja_id' => $classMeeting,
            ],
            [
                'nama' => 'Pentas Seni Neper',
                'deskripsi' => "Pementasan seni tahunan: band kelas, tari tradisional, teater, dan fashion show daur ulang.\n\nTiket masuk gratis untuk seluruh warga sekolah.",
                'lokasi' => 'Aula SMK Negeri 1 Cirebon',
                'waktu' => now()->addDays(21),
                'kuota' => 300,
                'program_kerja_id' => $pentas,
            ],
            [
                'nama' => 'Seminar Karier Bersama Alumni',
                'deskripsi' => "Sharing pengalaman alumni dalam dunia kerja IT & otomotif, tips membangun portofolio sejak sekolah, serta peluang magang.\n\nTerbuka untuk kelas X & XI.",
                'lokasi' => 'Radi Lab Komputer',
                'waktu' => now()->addDays(14),
                'kuota' => 100,
                'program_kerja_id' => $seminar,
            ],
            [
                'nama' => 'Kompetisi Internal Live Coding',
                'deskripsi' => "Seleksi internal tim lomba pemrograman: dua sesi problem solving secara onsite menggunakan laptop masing-masing.",
                'lokasi' => 'Lab RPL',
                'waktu' => now()->addDays(10),
                'kuota' => 40,
                'program_kerja_id' => null,
            ],
            [
                'nama' => 'Kunjungan Industri PT Mitra',
                'deskripsi' => "Kunjungan industri bagi siswa kelas XI untuk mengenal dunia kerja nyata di bidang teknologi dan manufaktur.",
                'lokasi' => 'Kawasan Industri Cirebon',
                'waktu' => now()->addDays(30),
                'kuota' => 80,
                'program_kerja_id' => null,
            ],
            [
                'nama' => 'Bakti Sosial & Kerja Bakti',
                'deskripsi' => "Aksi sosial ke panti asuhan dihar dan kerja bakti membersihkan lingkungan sekolah bersama seluruh pengurus OSIS.",
                'lokasi' => 'Panti Asuhan + Sekolah',
                'waktu' => now()->addDays(17),
                'kuota' => null,
                'program_kerja_id' => null,
            ],
        ];

        foreach ($data as $i => $d) {
            $event = Event::updateOrCreate(
                ['slug' => Str::slug($d['nama'])],
                [
                    'nama' => $d['nama'],
                    'deskripsi' => $d['deskripsi'],
                    'lokasi' => $d['lokasi'],
                    'waktu_mulai' => $d['waktu'],
                    'waktu_selesai' => $d['waktu']->copy()->addHours(6),
                    'kuota' => $d['kuota'],
                    'status' => 'terbit',
                    'program_kerja_id' => $d['program_kerja_id'],
                    'user_id' => $admin->id,
                ]
            );

            // Daftarkan beberapa siswa acak sebagai peserta
            if (! empty($siswa)) {
                $jumlah = $d['kuota'] ? min($d['kuota'], mt_rand(5, 60)) : mt_rand(5, 40);
                $peserta = collect($siswa)->random(min($jumlah, count($siswa)));
                $event->peserta()->syncWithoutDetaching($peserta->all());
            }
        }
    }
}
