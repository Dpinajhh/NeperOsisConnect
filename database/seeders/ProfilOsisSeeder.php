<?php

namespace Database\Seeders;

use App\Models\Kepengurusan;
use App\Models\ProfilOsis;
use Illuminate\Database\Seeder;

class ProfilOsisSeeder extends Seeder
{
    public function run(): void
    {
        ProfilOsis::updateOrCreate([], [
            'periode' => '2026/2027',
            'visi' => "Terwujudnya warga SMK Negeri 1 Cirebon yang aktif, kreatif, berkarakter, dan berprestasi melalui gerakan organisasi siswa yang inklusif serta membawa nama sekolah di tingkat daerah maupun nasional.",
            'misi' => "1. Menyelenggarakan kegiatan pelajar yang positif, edukatif, dan menyenangkan bagi seluruh warga sekolah.\n2. Memperkuat tali persaudaraan antarwarga kelas, jurusan, dan ekstrakurikuler.\n3. Menjadi wadah penampung dan penyalur aspirasi siswa kepada pihak sekolah.\n4. Mengembangkan minat & bakat siswa melalui lomba, pentas seni, dan kegiatan sosial.\n5. Membiasakan budaya disiplin, religius, dan peduli lingkungan di lingkungan sekolah.",
            'sejarah' => "OSIS SMK Negeri 1 Cirebon telah menjadi wadah gerak siswa sejak berdiri. Lewat perubahan dari masa STM Negeri Cirebon hingga kini, OSIS Neper terus melahirkan kegiatan yang mengangkat nama sekolah — mulai dari pentas seni, kompetisi keahlian, hingga aksi sosial lingkungan.",
            'sambutuan' => "Salam sejahtera untuk seluruh warga SMK Negeri 1 Cirebon. Sampurasun, Wargi Neper!\n\nMelalui Osis Connect, pengurus OSIS ingin hadir lebih dekat dengan setiap siswa. Semua informasi kegiatan, pengumuman, dan ruang berbagi cerita kini tersedia dalam satu genggaman. Mari jadikan organisasi sebagai pijakan untuk berani mencoba, berkarya, dan berprestasi.",
            'sambutuan_pengirim' => 'Pengurus OSIS SMK Negeri 1 Cirebon Periode 2026/2027',
            'alamat' => 'Jl. Perjuangan–Sunyaragi, Karyamulya, Kesamburan, Kota Cirebon, Jawa Barat',
            'email' => 'info@smkn1-cirebon.sch.id',
            'telepon' => '0231-123456',
            'instagram' => '@smkn1cirebon',
        ]);

        $pengurus = [
            ['Ahmad Fauzan', 'Ketua OSIS', 'Pengurus Harian'],
            ['Siti Nurhaliza', 'Wakil Ketua OSIS', 'Pengurus Harian'],
            ['Dewi Lestari', 'Sekretaris', 'Pengurus Harian'],
            ['Rizky Pratama', 'Bendahara', 'Pengurus Harian'],
            ['Aulia Rahma', 'Koordinator Kegiatan & Ekstrakurikuler', 'Kegiatan'],
            ['Bayu Setiawan', 'Koordinator Komunikasi & Informasi', 'Media'],
            ['Citra Kirana', 'Koordinator Prestasi Akademik', 'Akademik'],
            ['Eko Wibowo', 'Koordinator Olahraga & Seni', 'Olahraga & Seni'],
            ['Fitri Handayani', 'Koordinator Sosial & Lingkungan', 'Sosial'],
            ['Gilang Ramadhan', 'Koordinator Keagamaan', 'Keagamaan'],
            ['Hani Safitri', 'Anggota', 'Media'],
            ['Irfan Maulana', 'Anggota', 'Kegiatan'],
        ];

        $urutan = 1;
        foreach ($pengurus as [$nama, $jabatan, $bidang]) {
            Kepengurusan::updateOrCreate(
                ['nama' => $nama, 'jabatan' => $jabatan],
                ['bidang' => $bidang, 'urutan' => $urutan++]
            );
        }
    }
}
