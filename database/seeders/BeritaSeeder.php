<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $penulis = User::whereIn('role', ['admin', 'guru'])->pluck('id')->all();
        if (empty($penulis)) {
            return;
        }

        $kategori = Kategori::where('tipe', 'berita')->pluck('id')->all();

        $daftar = [
            [
                'judul' => 'Tim Osis Connect Raih Juara Lomba Inovasi Digital Sekolah',
                'ringkasan' => 'Tim pengembang Osis Connect berhasil meraih juara pertama pada lomba inovasi digital tingkat sekolah dengan aplikasi platform siswa terintegrasi.',
                'konten' => "Sebuah prestasi membanggakan kembali diraih oleh siswa-siswi sekolah kita. Tim pengembang Osis Connect berhasil menyabet juara pertama pada ajang Lomba Inovasi Digital tingkat sekolah yang diselenggarakan di aula utama kemarin.\n\nAplikasi yang mereka kembangkan merupakan platform terpadu bagi siswa untuk saling terhubung, membagikan pengumuman, serta menyajikan berita kegiatan sekolah secara cepat dan mudah diakses dari ponsel.\n\nKetua panitia lomba menyampaikan bahwa karya ini dinilai paling siap digunakan karena telah memperhatikan keamanan data dan tampilan yang ramah bagi pengguna muda.",
                'dilihat' => 487,
                'headline' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'judul' => 'Kerja Bakti Serentak Menyambut Tahun Ajaran Baru',
                'ringkasan' => 'Seluruh warga sekolah mengikuti kegiatan kerja bakti serentak membersihkan lingkungan kelas, taman, dan fasilitas sekolah.',
                'konten' => "Menyambut tahun ajaran baru, seluruh siswa dan guru mengikuti kegiatan kerja bakti serentak yang dilaksanakan hari Sabtu pagi.\n\nKegiatan yang diikuti sekitar seribu siswa ini diawali dengan apel bersama, kemudian pembagian area kerja. Setiap kelas bertanggung jawab atas kebersihan kelas, koridor, dan halaman sekolah masing-masing.\n\nKepala sekolah berharap kegiatan semacam ini dapat terus menjaga kenyamanan belajar dan menumbuhkan rasa memiliki terhadap lingkungan sekolah.",
                'dilihat' => 342,
                'headline' => false,
                'published_at' => now()->subDays(5),
            ],
            [
                'judul' => 'Ekstrakurikuler Robotika Membuka Pendaftaran Anggota Baru',
                'ringkasan' => 'Ekstrakurikuler robotika membuka kesempatan bagi siswa yang ingin belajar perakitan dan pemrograman robot.',
                'konten' => "Ekstrakurikuler Robotika kembali membuka pendaftaran anggota baru untuk semester ini. Pendaftaran dibuka bagi seluruh siswa tanpa memandang tingkat kelas.\n\nPeserta akan mempelajari perakitan robot dasar, pemrograman mikrokontroler, serta logika pemecahan masalah melalui proyek kelompok.\n\nKuota terbatas bagi tiga puluh siswa terpilih. Ayo bergabung dan kembangkan minatmu di bidang teknologi!",
                'dilihat' => 268,
                'headline' => false,
                'published_at' => now()->subDays(8),
            ],
            [
                'judul' => 'Semester Lalu, 87 Persen Siswa Naik ke Tingkat Berikutnya',
                'ringkasan' => 'Sekolah mencatat capaian akademik membaik dengan 87 persen siswa berhasil naik tingkat semester lalu.',
                'konten' => "Hasil evaluasi semester lalu menunjukkan capaian akademik sekolah cukup membanggakan. Sebanyak 87 persen siswa dinyatakan berhasil naik ke tingkat kelas berikutnya.\n\nKepala sekolah menyampaikan apresiasi kepada seluruh guru dan siswa atas kerja keras mereka. Beberapa program pendampingan belajar akan ditingkatkan agar capaian terus membaik.\n\nSiswa dengan nilai tertinggi di setiap jurusan juga menerima penghargaan pada penutupan semester.",
                'dilihat' => 415,
                'headline' => true,
                'published_at' => now()->subDays(11),
            ],
            [
                'judul' => 'Bazar Makanan, Hasil Karya Siswa Akuntansi Laris Manis',
                'ringkasan' => 'Bazar makanan yang digelar jurusan Akuntasi berhasil menyedot banyak pengunjung dan menunjukkan jiwa kewirausahaan siswa.',
                'konten' => "Bazar makanan tahunan jurusan Akuntansi berhasil menarik antusiasme tinggi dari pengunjung. Berbagai menu sederhana yang dijual siswa tersebut terjual habis beberapa jam setelah bazar dibuka.\n\nKegiatan ini menjadi media belajar langsung bagi siswa dalam menempuh ilmu akuntansi praktis, mulai dari perhitungan modal, pencatatan penjualan, hingga pembagian hasil.\n\nHasil penjualan akan disumbangkan untuk pengembangan sarana perpustakaan sekolah.",
                'dilihat' => 199,
                'headline' => false,
                'published_at' => now()->subDays(14),
            ],
            [
                'judul' => 'Draf: Panduan Teknis Yudisium dan Wisuda',
                'ringkasan' => 'Draf panduan teknis yudisium dan wisuda yang sedang disusun tim sekolah.',
                'konten' => "Dokumen ini masih berupa draf dan belum siap diterbitkan bagi siswa.\n\nTim sekolah sedang menyusun panduan teknis lengkap mencakup jadwal, pakaian, dan tata tertib yudisium.",
                'dilihat' => 0,
                'headline' => false,
                'published_at' => null,
            ],
        ];

        foreach ($daftar as $item) {
            $draft = $item['published_at'] === null;

            Berita::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($item['judul'])],
                [
                    'user_id' => fake()->randomElement($penulis),
                    'kategori_id' => $kategori ? fake()->randomElement($kategori) : null,
                    'judul' => $item['judul'],
                    'ringkasan' => $item['ringkasan'],
                    'konten' => $item['konten'],
                    'cover' => null,
                    'status' => $draft ? 'draft' : 'published',
                    'is_headline' => $item['headline'],
                    'published_at' => $item['published_at'],
                    'dilihat' => $item['dilihat'],
                ]
            );
        }
    }
}
