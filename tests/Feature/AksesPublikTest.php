<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Komentar;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Akses publik (tamu) ke pengumuman & berita, plus hitungan aktivitas
 * profil publik yang menghormati visibilitas postingan.
 */
class AksesPublikTest extends TestCase
{
    use RefreshDatabase;

    // ─── Pengumuman ───────────────────────────────────────────

    public function test_tamu_dapat_membuka_daftar_pengumuman_untuk_semua(): void
    {
        $kelas = Kelas::factory()->create();

        Pengumuman::factory()->create([
            'judul' => 'Pengumuman Seluruh Sekolah',
            'slug' => 'pengumuman-seluruh-sekolah',
            'target' => 'semua',
        ]);

        Pengumuman::factory()->create([
            'judul' => 'Pengumuman Khusus Kelas',
            'slug' => 'pengumuman-khusus-kelas',
            'target' => 'kelas',
            'kelas_id' => $kelas->id,
        ]);

        $this->get(route('pengumuman.index'))
            ->assertOk()
            ->assertSee('Pengumuman Seluruh Sekolah')
            ->assertDontSee('Pengumuman Khusus Kelas');
    }

    public function test_tamu_dapat_membuka_detail_pengumuman_tanpa_mencatat_riwayat(): void
    {
        $pengumuman = Pengumuman::factory()->create([
            'judul' => 'Terbuka Untuk Tamu',
            'slug' => 'terbuka-untuk-tamu',
            'dilihat' => 0,
        ]);

        $this->get(route('pengumuman.detail', $pengumuman))
            ->assertOk()
            ->assertSee('Terbuka Untuk Tamu');

        // Dilihat tetap dihitung, tapi tidak ada riwayat baca milik siapa pun
        $this->assertSame(1, $pengumuman->fresh()->dilihat);
        $this->assertDatabaseCount('pengumuman_baca', 0);
    }

    public function test_tamu_tidak_boleh_membuka_pengumuman_kelas(): void
    {
        $kelas = Kelas::factory()->create();

        $pengumuman = Pengumuman::factory()->create([
            'slug' => 'khusus-kelas-tertentu',
            'target' => 'kelas',
            'kelas_id' => $kelas->id,
        ]);

        $this->get(route('pengumuman.detail', $pengumuman))->assertForbidden();
    }

    public function test_pengumuman_belum_terbit_tidak_bisa_dibuka_lewat_url_langsung(): void
    {
        $siswa = User::factory()->siswa()->create();

        $pengumuman = Pengumuman::factory()->create([
            'slug' => 'masih-dijadwalkan',
            'published_at' => now()->addWeek(),
        ]);

        $this->actingAs($siswa)
            ->get(route('pengumuman.detail', $pengumuman))
            ->assertNotFound();
    }

    // ─── Berita ───────────────────────────────────────────────

    public function test_tamu_dapat_membuka_berita_dan_detailnya(): void
    {
        $berita = Berita::factory()->create([
            'judul' => 'Berita Terbuka Untuk Tamu',
            'slug' => 'berita-terbuka-untuk-tamu',
        ]);

        $this->get(route('berita.index'))
            ->assertOk()
            ->assertSee('Berita Terbuka Untuk Tamu');

        $this->get(route('berita.detail', $berita))
            ->assertOk()
            ->assertSee('Berita Terbuka Untuk Tamu');
    }

    public function test_tamu_tidak_boleh_membuka_berita_draft(): void
    {
        $draft = Berita::factory()->draft()->create(['slug' => 'draft-untuk-tamu']);

        $this->get(route('berita.detail', $draft))->assertNotFound();
    }

    public function test_berita_terjadwal_belum_bisa_dibuka(): void
    {
        $siswa = User::factory()->siswa()->create();

        $berita = Berita::factory()->create([
            'slug' => 'berita-terjadwal',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->actingAs($siswa)
            ->get(route('berita.detail', $berita))
            ->assertNotFound();
    }

    // ─── Hitungan aktivitas di profil publik ──────────────────

    public function test_hitungan_post_profil_menghormati_visibilitas(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();

        $pemilik = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasA->id]);
        $tetangga = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasB->id]);

        Post::factory()->visibilitas('sekolah')->create(['user_id' => $pemilik->id]);
        Post::factory()->visibilitas('kelas')->create(['user_id' => $pemilik->id]);

        // Pemilik melihat seluruh postingannya sendiri
        $this->actingAs($pemilik)
            ->get(route('profil.show', $pemilik))
            ->assertViewHas('jumlahPost', 2);

        // Siswa beda kelas hanya melihat yang ber-visibilitas sekolah
        $this->actingAs($tetangga)
            ->get(route('profil.show', $pemilik))
            ->assertViewHas('jumlahPost', 1);

        // Pengelola melihat semuanya
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('profil.show', $pemilik))
            ->assertViewHas('jumlahPost', 2);
    }

    public function test_hitungan_komentar_profil_menghormati_visibilitas(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();

        $pemilik = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasA->id]);
        $tetangga = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasB->id]);

        $postSekolah = Post::factory()->visibilitas('sekolah')->create(['user_id' => $tetangga->id]);
        $postKelas = Post::factory()->visibilitas('kelas')->create(['user_id' => $pemilik->id]);

        Komentar::factory()->create(['user_id' => $pemilik->id, 'post_id' => $postSekolah->id]);
        Komentar::factory()->create(['user_id' => $pemilik->id, 'post_id' => $postKelas->id]);

        $this->actingAs($pemilik)
            ->get(route('profil.show', $pemilik))
            ->assertViewHas('jumlahKomentar', 2);

        $this->actingAs($tetangga)
            ->get(route('profil.show', $pemilik))
            ->assertViewHas('jumlahKomentar', 1);
    }

    public function test_visibilitas_untuk_tamu_hanya_postingan_sekolah(): void
    {
        $pemilik = User::factory()->siswa()->create();

        Post::factory()->visibilitas('sekolah')->create(['user_id' => $pemilik->id]);
        Post::factory()->visibilitas('kelas')->create(['user_id' => $pemilik->id]);
        Post::factory()->visibilitas('jurusan')->create(['user_id' => $pemilik->id]);

        $this->assertSame(1, Post::query()->terlihatUntuk(null)->count());
    }
}
