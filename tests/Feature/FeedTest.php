<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Komentar;
use App\Models\Notifikasi;
use App\Models\Post;
use App\Models\Topik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feed sosial: posting, suka, dan komentar.
 */
class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_dapat_membuat_postingan(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->post(route('posts.store'), [
                'konten' => 'Halo teman-teman, besok kita kerja bakti ya!',
                'visibilitas' => 'sekolah',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'user_id' => $siswa->id,
            'konten' => 'Halo teman-teman, besok kita kerja bakti ya!',
            'visibilitas' => 'sekolah',
        ]);
    }

    public function test_postingan_kosong_ditolak(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->from(route('feed'))
            ->post(route('posts.store'), [
                'konten' => '',
                'visibilitas' => 'sekolah',
            ])
            ->assertSessionHasErrors('konten');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_visibilitas_kelas_ditolak_bila_siswa_belum_punya_kelas(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa', 'kelas_id' => null]);

        $this->actingAs($siswa)
            ->from(route('feed'))
            ->post(route('posts.store'), [
                'konten' => 'Postingan terbatas kelas.',
                'visibilitas' => 'kelas',
            ])
            ->assertSessionHasErrors('visibilitas');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_konten_hashtag_otomatis_menjadi_topik(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->post(route('posts.store'), [
            'konten' => 'Seru sekali acara #ClassMeeting tahun ini!',
            'visibilitas' => 'sekolah',
        ]);

        $this->assertDatabaseHas('topik', ['slug' => 'classmeeting']);
    }

    public function test_tombol_suka_menyalakan_dan_mematikan(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['jumlah_suka' => 0]);

        // Suka pertama → aktif
        $this->actingAs($siswa)
            ->post(route('posts.suka', $post))
            ->assertOk()
            ->assertJson(['disukai' => true, 'jumlah_suka' => 1]);

        $this->assertDatabaseHas('post_suka', [
            'post_id' => $post->id,
            'user_id' => $siswa->id,
        ]);

        // Suka kedua → batal
        $this->actingAs($siswa)
            ->post(route('posts.suka', $post))
            ->assertOk()
            ->assertJson(['disukai' => false, 'jumlah_suka' => 0]);

        $this->assertDatabaseMissing('post_suka', [
            'post_id' => $post->id,
            'user_id' => $siswa->id,
        ]);
    }

    public function test_suka_mengirim_notifikasi_ke_pemilik_postingan(): void
    {
        $pemilik = User::factory()->siswa()->create();
        $penyuka = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($penyuka)->post(route('posts.suka', $post))->assertOk();

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $pemilik->id,
            'tipe' => 'suka',
        ]);
    }

    public function test_suka_postingan_sendiri_tidak_membuat_notifikasi(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)->post(route('posts.suka', $post))->assertOk();

        $this->assertDatabaseCount('notifikasi', 0);
    }

    public function test_siswa_dapat_mengomentari_postingan(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['jumlah_komentar' => 0]);

        $this->actingAs($siswa)
            ->post(route('posts.komentar', $post), ['konten' => 'Semangat terus!'])
            ->assertOk()
            ->assertJson(['konten' => 'Semangat terus!']);

        $this->assertDatabaseHas('komentar', [
            'post_id' => $post->id,
            'user_id' => $siswa->id,
            'konten' => 'Semangat terus!',
        ]);

        $this->assertSame(1, $post->fresh()->jumlah_komentar);
    }

    public function test_komentar_kosong_ditolak(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create();

        $this->actingAs($siswa)
            ->post(route('posts.komentar', $post), ['konten' => ''])
            ->assertSessionHasErrors('konten');

        $this->assertDatabaseCount('komentar', 0);
    }

    public function test_balasan_komentar_menyimpan_parent_id(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create();
        $induk = Komentar::factory()->create(['post_id' => $post->id]);

        $this->actingAs($siswa)
            ->post(route('posts.komentar', $post), [
                'konten' => 'Setuju banget!',
                'parent_id' => $induk->id,
            ])
            ->assertOk()
            ->assertJsonPath('parent_id', $induk->id);
    }

    public function test_pemilik_dapat_menghapus_postingannya(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)
            ->delete(route('posts.destroy', $post))
            ->assertRedirect();

        // Post memakai SoftDeletes — baris tetap ada, deleted_at terisi
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_siswa_lain_tidak_boleh_menghapus_postingan_orang(): void
    {
        $pemilik = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($lain)
            ->delete(route('posts.destroy', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_pengelola_boleh_menghapus_postingan_siswa(): void
    {
        $siswa = User::factory()->siswa()->create();
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($admin)
            ->delete(route('posts.destroy', $post))
            ->assertRedirect();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_pemilik_dapat_mengubah_postingannya(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create([
            'user_id' => $siswa->id,
            'konten' => 'Teks lama yang akan diperbaiki.',
            'visibilitas' => 'sekolah',
        ]);

        $this->actingAs($siswa)
            ->get(route('posts.edit', $post))
            ->assertOk()
            ->assertSee('Teks lama yang akan diperbaiki.');

        $this->actingAs($siswa)
            ->put(route('posts.update', $post), [
                'konten' => 'Teks baru yang sudah diperbaiki.',
                'visibilitas' => 'sekolah',
            ])
            ->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'konten' => 'Teks baru yang sudah diperbaiki.',
        ]);
    }

    public function test_siswa_lain_tidak_boleh_mengubah_postingan_orang(): void
    {
        $pemilik = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create();
        $post = Post::factory()->create([
            'user_id' => $pemilik->id,
            'konten' => 'Tulisan pemilik.',
        ]);

        $this->actingAs($lain)->get(route('posts.edit', $post))->assertForbidden();
        $this->actingAs($lain)
            ->put(route('posts.update', $post), [
                'konten' => 'Coba ubah.',
                'visibilitas' => 'sekolah',
            ])
            ->assertForbidden();

        $this->assertSame('Tulisan pemilik.', $post->fresh()->konten);
    }

    public function test_pengelola_tidak_boleh_mengubah_postingan_siswa(): void
    {
        $siswa = User::factory()->siswa()->create();
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($admin)->get(route('posts.edit', $post))->assertForbidden();
        $this->actingAs($admin)
            ->put(route('posts.update', $post), [
                'konten' => 'Diubah admin.',
                'visibilitas' => 'sekolah',
            ])
            ->assertForbidden();
    }

    public function test_ubah_postingan_menyesuaikan_hitungan_topik(): void
    {
        $siswa = User::factory()->siswa()->create();
        $lama = Topik::factory()->create(['nama' => 'lama', 'slug' => 'lama', 'jumlah_post' => 1]);
        $tetap = Topik::factory()->create(['nama' => 'tetap', 'slug' => 'tetap', 'jumlah_post' => 1]);

        $post = Post::factory()->create([
            'user_id' => $siswa->id,
            'konten' => 'Cerita #lama dan #tetap.',
        ]);
        $post->topik()->attach([$lama->id, $tetap->id]);

        $this->actingAs($siswa)
            ->put(route('posts.update', $post), [
                'konten' => 'Cerita #tetap dan #baru.',
                'visibilitas' => 'sekolah',
            ])
            ->assertRedirect(route('posts.show', $post));

        $this->assertSame(0, $lama->fresh()->jumlah_post);
        $this->assertSame(1, $tetap->fresh()->jumlah_post);
        $this->assertSame(1, Topik::where('slug', 'baru')->value('jumlah_post'));

        $this->assertDatabaseMissing('post_topik', ['post_id' => $post->id, 'topik_id' => $lama->id]);
        $this->assertDatabaseHas('post_topik', ['post_id' => $post->id, 'topik_id' => $tetap->id]);
    }

    public function test_ubah_visibilitas_kelas_ditolak_bila_siswa_belum_punya_kelas(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa', 'kelas_id' => null]);
        $post = Post::factory()->create([
            'user_id' => $siswa->id,
            'konten' => 'Teks asli.',
        ]);

        $this->actingAs($siswa)
            ->from(route('posts.edit', $post))
            ->put(route('posts.update', $post), [
                'konten' => 'Teks baru.',
                'visibilitas' => 'kelas',
            ])
            ->assertSessionHasErrors('visibilitas');

        $this->assertSame('Teks asli.', $post->fresh()->konten);
    }

    public function test_postingan_visibilitas_kelas_hanya_terlihat_sesama_kelas(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();

        $siswaA = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasA->id]);
        $siswaB = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasB->id]);

        Post::factory()->create([
            'user_id' => $siswaA->id,
            'konten' => 'RAHASIA_KELAS_A saja.',
            'visibilitas' => 'kelas',
        ]);

        // Teman sekelas bisa melihat
        $this->actingAs($siswaA)->get(route('feed'))->assertSee('RAHASIA_KELAS_A');

        // Kelas lain tidak bisa melihat
        $this->actingAs($siswaB)->get(route('feed'))->assertDontSee('RAHASIA_KELAS_A');
    }

    public function test_notifikasi_komentar_terkirim_ke_pemilik_postingan(): void
    {
        $pemilik = User::factory()->siswa()->create();
        $pengomentar = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($pengomentar)
            ->post(route('posts.komentar', $post), ['konten' => 'Keren!']);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $pemilik->id,
            'tipe' => 'komentar',
        ]);

        // Pengomentar sendiri tidak dapat notifikasi
        $this->assertSame(1, Notifikasi::count());
    }
}
