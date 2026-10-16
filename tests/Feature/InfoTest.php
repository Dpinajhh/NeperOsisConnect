<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengumuman (dengan target kelas/jurusan) & Berita (draft/terbit).
 */
class InfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengumuman_untuk_semua_terlihat_oleh_setiap_siswa(): void
    {
        $siswa = User::factory()->siswa()->create();

        Pengumuman::factory()->create([
            'judul' => 'Rapat OSIS Untuk Semua',
            'slug' => 'rapat-osis-untuk-semua',
            'target' => 'semua',
        ]);

        $this->actingAs($siswa)
            ->get(route('pengumuman.index'))
            ->assertOk()
            ->assertSee('Rapat OSIS Untuk Semua');
    }

    public function test_pengumuman_khusus_kelas_hanya_terlihat_kelas_tujuan(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();

        $siswaA = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasA->id]);
        $siswaB = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasB->id]);

        Pengumuman::factory()->create([
            'judul' => 'Khusus Kelas A Saja',
            'slug' => 'khusus-kelas-a-saja',
            'target' => 'kelas',
            'kelas_id' => $kelasA->id,
        ]);

        $this->actingAs($siswaA)
            ->get(route('pengumuman.index'))
            ->assertSee('Khusus Kelas A Saja');

        $this->actingAs($siswaB)
            ->get(route('pengumuman.index'))
            ->assertDontSee('Khusus Kelas A Saja');
    }

    public function test_pengumuman_khusus_jurusan_hanya_terlihat_jurusan_tujuan(): void
    {
        $jurusanPplg = Jurusan::factory()->create(['kode' => 'PPLG']);
        $jurusanTkj = Jurusan::factory()->create(['kode' => 'TKJ']);

        $kelasPplg = Kelas::factory()->create(['jurusan_id' => $jurusanPplg->id]);
        $kelasTkj = Kelas::factory()->create(['jurusan_id' => $jurusanTkj->id]);

        $siswaPplg = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasPplg->id]);
        $siswaTkj = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasTkj->id]);

        Pengumuman::factory()->create([
            'judul' => 'Khusus Jurusan PPLG',
            'slug' => 'khusus-jurusan-pplg',
            'target' => 'jurusan',
            'jurusan_id' => $jurusanPplg->id,
        ]);

        $this->actingAs($siswaPplg)
            ->get(route('pengumuman.index'))
            ->assertSee('Khusus Jurusan PPLG');

        $this->actingAs($siswaTkj)
            ->get(route('pengumuman.index'))
            ->assertDontSee('Khusus Jurusan PPLG');
    }

    public function test_siswa_lain_tidak_boleh_membuka_detail_pengumuman_kelas_lain(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();

        $siswaB = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasB->id]);

        $pengumuman = Pengumuman::factory()->create([
            'slug' => 'rahasia-kelas-a',
            'target' => 'kelas',
            'kelas_id' => $kelasA->id,
        ]);

        $this->actingAs($siswaB)
            ->get(route('pengumuman.detail', $pengumuman))
            ->assertForbidden();
    }

    public function test_pengumuman_belum_terbit_tidak_tampil(): void
    {
        $siswa = User::factory()->siswa()->create();

        Pengumuman::factory()->create([
            'judul' => 'Belum Terbit Nih',
            'slug' => 'belum-terbit-nih',
            'published_at' => now()->addWeek(),
        ]);

        $this->actingAs($siswa)
            ->get(route('pengumuman.index'))
            ->assertDontSee('Belum Terbit Nih');
    }

    public function test_membuka_detail_pengumuman_menandai_sudah_dibaca(): void
    {
        $siswa = User::factory()->siswa()->create();

        $pengumuman = Pengumuman::factory()->create([
            'slug' => 'pengumuman-uji-baca',
            'dilihat' => 0,
        ]);

        $this->actingAs($siswa)
            ->get(route('pengumuman.detail', $pengumuman))
            ->assertOk();

        $this->assertDatabaseHas('pengumuman_baca', [
            'pengumuman_id' => $pengumuman->id,
            'user_id' => $siswa->id,
        ]);

        $this->assertSame(1, $pengumuman->fresh()->dilihat);
    }

    public function test_berita_terbit_tampil_dan_draft_tidak(): void
    {
        $siswa = User::factory()->siswa()->create();

        Berita::factory()->create([
            'judul' => 'Berita Terbit Resmi',
            'slug' => 'berita-terbit-resmi',
        ]);

        Berita::factory()->draft()->create([
            'judul' => 'Draft Belum Tayang',
            'slug' => 'draft-belum-tayang',
        ]);

        $this->actingAs($siswa)
            ->get(route('berita.index'))
            ->assertOk()
            ->assertSee('Berita Terbit Resmi')
            ->assertDontSee('Draft Belum Tayang');
    }

    public function test_siswa_tidak_bisa_membuka_detail_berita_draft(): void
    {
        $siswa = User::factory()->siswa()->create();

        $draft = Berita::factory()->draft()->create(['slug' => 'draft-rahasia']);

        $this->actingAs($siswa)
            ->get(route('berita.detail', $draft))
            ->assertNotFound();
    }

    public function test_berita_headline_tampil_di_halaman_berita(): void
    {
        $siswa = User::factory()->siswa()->create();

        Berita::factory()->headline()->create([
            'judul' => 'Headline Utama Sekolah',
            'slug' => 'headline-utama-sekolah',
        ]);

        $this->actingAs($siswa)
            ->get(route('berita.index'))
            ->assertOk()
            ->assertSee('Headline Utama Sekolah');
    }

    public function test_berita_detail_menambah_jumlah_dilihat(): void
    {
        $siswa = User::factory()->siswa()->create();

        $berita = Berita::factory()->create([
            'slug' => 'berita-uji-dilihat',
            'dilihat' => 5,
        ]);

        $this->actingAs($siswa)
            ->get(route('berita.detail', $berita))
            ->assertOk();

        $this->assertSame(6, $berita->fresh()->dilihat);
    }
}
