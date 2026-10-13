<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    /**
     * Pengumuman contoh. Dua untuk seluruh sekolah, lainnya bertarget
     * kelas / jurusan tertentu.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            return;
        }

        $kategori = Kategori::where('tipe', 'pengumuman')->pluck('id')->all();
        $kelas = Kelas::where('nama', 'like', 'XII%')->first();
        $jurusan = \App\Models\Jurusan::where('kode', 'PPLG')->first();

        // 1. Pengumuman untuk seluruh sekolah + PINNED
        Pengumuman::updateOrCreate(
            ['slug' => 'rapat-awal-tahun-ajaran-semester-genap'],
            [
                'user_id' => $admin->id,
                'kategori_id' => $kategori ? fake()->randomElement($kategori) : null,
                'judul' => 'Rapat Awal Tahun Ajaran Semester Genap',
                'konten' => $this->konten(
                    'Diumumkan kepada seluruh siswa bahwa rapat awal tahun ajaran semester genap akan dilaksanakan pada hari Senin di aula sekolah. Seluruh pengurus OSIS, ketua kelas, dan perwakilan ekstrakurikuler wajib hadir.',
                    'Agenda rapat: laporan kegiatan semester pertama, rencana kerja semester genap, serta pembagian tugas pengurus baru.'
                ),
                'target' => 'semua',
                'is_pinned' => true,
                'published_at' => now()->subDays(2),
                'dilihat' => 318,
            ]
        );

        // 2. Pengumuman umum
        Pengumuman::updateOrCreate(
            ['slug' => 'jadwal-layanan-konseling-siswa'],
            [
                'user_id' => $admin->id,
                'judul' => 'Jadwal Layanan Konseling Siswa Bulan Ini',
                'konten' => $this->konten(
                    'Layanan konseling siswa buka setiap hari Selasa dan Kamis, pukul 10.00-13.00 di ruang BK. Siswa yang ingin melakukan konsultasi dapat mendaftar melalui wali kelas.',
                    'Percakapan bersifat rahasia. Jangan ragu untuk menghubungi guru BK apabila ada kendala pribadi, pertemanan, maupun akademik.'
                ),
                'target' => 'semua',
                'is_pinned' => false,
                'published_at' => now()->subDays(6),
                'dilihat' => 192,
            ]
        );

        // 3. Khusus kelas XII
        if ($kelas) {
            Pengumuman::updateOrCreate(
                ['slug' => 'pengumpulan-berkas-pkl-kelas-xii'],
                [
                    'user_id' => $admin->id,
                    'judul' => 'Pengumpulan Berkas Prakarya Kerja Lapangan (PKL)',
                    'konten' => $this->konten(
                        'Khusus siswa kelas XII, berkas PKL terdiri atas surat pengantar, jurnal harian, dan laporan akhir wajib dikumpulkan paling lambat akhir bulan ini ke ruang guru.',
                        'Berkas yang terlambat menumpuk dapat memengaruhi kelulusan. Segera hubungi koordinator jurusan apabila ada kendala.'
                    ),
                    'target' => 'kelas',
                    'kelas_id' => $kelas->id,
                    'is_pinned' => false,
                    'published_at' => now()->subDay(),
                    'dilihat' => 74,
                ]
            );
        }

        // 4. Khusus jurusan PPLG
        if ($jurusan) {
            Pengumuman::updateOrCreate(
                ['slug' => 'seleksi-internal-kompetisi-pemrograman'],
                [
                    'user_id' => $admin->id,
                    'judul' => 'Seleksi Internal Tim Kompetisi Pemrograman',
                    'konten' => $this->konten(
                        'Jurusan PPLG membuka seleksi internal untuk tim yang akan mewakili sekolah pada kompetisi pemrograman tingkat kabupaten. Seleksi tahap pertama berupa tes logika dan pemrograman dasar.',
                        'Kuota terbatas. Siswa yang berminat segera hubungi guru produktif masing-masing.'
                    ),
                    'target' => 'jurusan',
                    'jurusan_id' => $jurusan->id,
                    'is_pinned' => false,
                    'published_at' => now()->subDays(3),
                    'dilihat' => 126,
                ]
            );
        }

        // 5. Pengumuman terjadwal (belum terbit)
        Pengumuman::updateOrCreate(
            ['slug' => 'pengumuman-upacara-bendera'],
            [
                'user_id' => $admin->id,
                'judul' => 'Upacara Peringatan Hari Pendidikan Nasional',
                'konten' => $this->konten(
                    'Upacara peringahan Hari Pendidikan Nasional akan dilaksanakan seluruh warga sekolah. Siswa hadir tepat waktu dengan seragam lengkap.',
                    'Petugas upacara wajib hadir pukul 06.30 WIB.'
                ),
                'target' => 'semua',
                'is_pinned' => false,
                'published_at' => now()->addDays(4),
                'dilihat' => 0,
            ]
        );
    }

    private function konten(string ...$paragraf): string
    {
        return implode("\n\n", $paragraf);
    }
}
