<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pencarian global (/cari): postingan, berita, pengumuman, topik, siswa.
 */
class CariTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_cari_tanpa_kata_kunci_tampil(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('cari'))
            ->assertOk()
            ->assertSee('Hasil Pencarian');
    }

    public function test_mencari_postingan_siswa(): void
    {
        $siswa = User::factory()->siswa()->create();

        Post::factory()->create([
            'konten' => 'Latihan robotik menjelang kompetisi nasional.',
            'visibilitas' => 'sekolah',
        ]);

        $this->actingAs($siswa)
            ->get(route('cari', ['q' => 'robotik']))
            ->assertOk()
            ->assertSee('Latihan robotik');
    }

    public function test_mencari_berita_dan_pengumuman(): void
    {
        $siswa = User::factory()->siswa()->create();

        Berita::factory()->create([
            'judul' => 'Turnamen basket antar kelas',
            'slug' => 'turnamen-basket-antar-kelas',
        ]);

        Pengumuman::factory()->create([
            'judul' => 'Pendaftaran turnamen basket dibuka',
            'slug' => 'pendaftaran-turnamen-basket-dibuka',
        ]);

        $this->actingAs($siswa)
            ->get(route('cari', ['q' => 'basket']))
            ->assertOk()
            ->assertSee('Turnamen basket antar kelas')
            ->assertSee('Pendaftaran turnamen basket dibuka');
    }

    public function test_mencari_siswa_berdasarkan_nama(): void
    {
        $siswa = User::factory()->siswa()->create();
        $target = User::factory()->siswa()->create(['name' => 'Rizky Pratama']);

        $this->actingAs($siswa)
            ->get(route('cari', ['q' => 'Rizky']))
            ->assertOk()
            ->assertSee('Rizky Pratama');
    }

    public function test_hasil_pencarian_menghormati_visibilitas_kelas(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();

        $siswaA = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasA->id]);
        $siswaB = User::factory()->create(['role' => 'siswa', 'kelas_id' => $kelasB->id]);

        Post::factory()->create([
            'user_id' => $siswaA->id,
            'konten' => 'RENCANA_INTERNAL_KELAS_A minggu ini.',
            'visibilitas' => 'kelas',
        ]);

        // Teman sekelas menemukan hasilnya
        $this->actingAs($siswaA)
            ->get(route('cari', ['q' => 'RENCANA_INTERNAL']))
            ->assertSee('RENCANA_INTERNAL_KELAS_A');

        // Kelas lain tidak menemukan
        $this->actingAs($siswaB)
            ->get(route('cari', ['q' => 'RENCANA_INTERNAL']))
            ->assertDontSee('RENCANA_INTERNAL_KELAS_A');
    }

    public function test_kata_kunci_tanpa_hasil_menampilkan_pesan_kosong(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('cari', ['q' => 'zzzqqqxx']))
            ->assertOk()
            ->assertSee('Tidak ada hasil');
    }

    public function test_kata_kunci_terlalu_pendek_ditolak(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('cari', ['q' => 'a']))
            ->assertSessionHasErrors('q');
    }

    public function test_berita_draft_tidak_muncul_di_pencarian(): void
    {
        $siswa = User::factory()->siswa()->create();

        // Judul sengaja dibuat berbeda dari kata kunci, karena kata kunci
        // selalu ikut tampil di halaman hasil pencarian.
        Berita::factory()->draft()->create([
            'judul' => 'Temuan Investigasi Belum Tayang',
            'slug' => 'temuan-investigasi-belum-tayang',
            'ringkasan' => 'Rangkuman temuan rahasia panitia.',
        ]);

        $this->actingAs($siswa)
            ->get(route('cari', ['q' => 'temuan']))
            ->assertOk()
            ->assertSee('Tidak ada hasil')
            ->assertDontSee('Temuan Investigasi Belum Tayang');
    }

    public function test_tamu_tidak_dapat_mencari(): void
    {
        $this->get(route('cari', ['q' => 'osis']))->assertRedirect(route('login'));
    }
}
