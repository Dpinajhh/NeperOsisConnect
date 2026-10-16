<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Beranda publik menampilkan statistik & pengumuman terbaru.
     */
    public function test_beranda_publik_tampil_dengan_benar(): void
    {
        User::factory()->siswa()->count(3)->create();
        Berita::factory()->count(2)->create();
        Pengumuman::factory()->create(['judul' => 'Pengumuman Beranda Uji', 'slug' => 'pengumuman-beranda-uji']);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Osis Connect')
            ->assertSee('Pengumuman Beranda Uji');
    }
}
