<?php

namespace Database\Seeders;

use App\Models\Polling;
use App\Models\User;
use Illuminate\Database\Seeder;

class PollingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            return;
        }

        $siswa = User::where('role', 'siswa')->pluck('id')->all();

        $data = [
            [
                'pertanyaan' => 'Tema class meeting semester ini sebaiknya apa?',
                'deskripsi' => 'Pilih tema yang menurutmu paling seru untuk kegiatan antar kelas.',
                'pilihan' => ['Olahraga & Juang', 'Seni & Kreativitas', 'Teknologi & Game', 'Sosial & Lingkungan'],
            ],
            [
                'pertanyaan' => 'Jam ekstrakurikuler yang paling pas?',
                'deskripsi' => 'Ekstrakurikuler di sekolah diusulkan pada jam paling nyaman bagi siswa.',
                'pilihan' => ['Setelah jam pelajaran siang', 'Setelah jam pelajaran sore', 'Sabtu pagi'],
            ],
            [
                'pertanyaan' => 'Sosial media mana yang paling kamu pakai untuk info sekolah?',
                'deskripsi' => 'Hasil polling membantu OSIS menentukan kanal informasi terbaik.',
                'pilihan' => ['Instagram', 'TikTok', 'WhatsApp Grup Kelas', 'Website sekolah'],
            ],
        ];

        foreach ($data as $d) {
            $polling = Polling::updateOrCreate(
                ['pertanyaan' => $d['pertanyaan']],
                [
                    'slug' => \Illuminate\Support\Str::slug(\Illuminate\Support\Str::limit($d['pertanyaan'], 60, '')),
                    'deskripsi' => $d['deskripsi'] ?? null,
                    'status' => 'terbit',
                    'user_id' => $admin->id,
                ]
            );

            $polling->pilihan()->delete();

            $urutan = 1;
            foreach ($d['pilihan'] as $teks) {
                $polling->pilihan()->create(['teks' => $teks, 'urutan' => $urutan++]);
            }

            // Beri suara acak pada 20-40 siswa
            $pilihan = $polling->pilihan()->pluck('id')->all();
            $jumlahSuara = min(count($siswa), mt_rand(20, 40));
            foreach (collect($siswa)->random($jumlahSuara) as $userId) {
                $polling->suara()->create([
                    'pilihan_id' => fake()->randomElement($pilihan),
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
