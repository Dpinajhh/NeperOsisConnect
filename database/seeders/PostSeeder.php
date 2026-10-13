<?php

namespace Database\Seeders;

use App\Models\Komentar;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = User::where('role', 'siswa')->pluck('id')->all();
        $semuaUser = User::pluck('id')->all();

        if (empty($siswa)) {
            return;
        }

        $posts = Post::factory()
            ->count(30)
            ->create(['user_id' => fn () => fake()->randomElement($siswa)]);

        foreach ($posts as $post) {
            // ── Suka ──────────────────────────────────────
            $pencari = collect($semuaUser)->random(mt_rand(0, 15));
            foreach ($pencari as $userId) {
                $post->disukaiOleh()->syncWithoutDetaching([$userId]);
            }

            // ── Komentar utama ────────────────────────────
            $jumlahKomentar = mt_rand(0, 8);
            for ($i = 0; $i < $jumlahKomentar; $i++) {
                $komentar = Komentar::create([
                    'user_id' => fake()->randomElement($semuaUser),
                    'post_id' => $post->id,
                    'konten' => fake()->randomElement([
                        'Setuju banget!',
                        'Keren infonya, terima kasih.',
                        'Infonya jelas sekali.',
                        'Mantap, semangat terus!',
                        'Boleh bagi-bagi detailnya?',
                        'Siap, akan saya cek.',
                        'Wah menarik juga.',
                        'Sudah kabari teman-teman yang lain.',
                    ]),
                ]);

                // Balasan acak (1-2 per komentar)
                if (fake()->boolean(30)) {
                    Komentar::create([
                        'user_id' => $post->user_id,
                        'post_id' => $post->id,
                        'parent_id' => $komentar->id,
                        'konten' => fake()->randomElement([
                            'Terima kasih sudah membaca!',
                            'Siap, nanti saya kabari lagi.',
                            'Boleh, coba hubungi saya langsung.',
                            'Makasih dukungannya!',
                        ]),
                    ]);
                }
            }

            // ── Sinkronkan counter ────────────────────────
            $post->update([
                'jumlah_suka' => $post->disukaiOleh()->count(),
                'jumlah_komentar' => $post->komentar()->count(),
            ]);
        }
    }
}
