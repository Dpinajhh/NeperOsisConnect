<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kontrol akses berbasis peran: siswa, guru, admin.
 */
class AksesTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_publik_dapat_diakses_tanpa_login(): void
    {
        $this->get(route('beranda'))->assertOk();
    }

    public function test_halaman_publik_lainnya_terbuka_untuk_tamu(): void
    {
        foreach ([
            route('profil-osis'),
            route('program-kerja'),
            route('event'),
            route('polling'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_siswa_tidak_boleh_masuk_panel_pengelola(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('admin.beranda'))
            ->assertForbidden();
    }

    public function test_siswa_tidak_boleh_membuka_kelola_konten(): void
    {
        $siswa = User::factory()->siswa()->create();

        foreach ([
            route('admin.pengumuman.kelola'),
            route('admin.berita.kelola'),
            route('admin.laporan.kelola'),
            route('admin.pengguna.kelola'),
            route('admin.program-kerja.kelola'),
            route('admin.event.kelola'),
            route('admin.polling.kelola'),
            route('admin.profil-osis.edit'),
        ] as $url) {
            $this->actingAs($siswa)->get($url)->assertForbidden();
        }
    }

    public function test_guru_dapat_membuka_seluruh_panel_pengelola(): void
    {
        $guru = User::factory()->guru()->create();

        foreach ([
            route('admin.beranda'),
            route('admin.pengumuman.kelola'),
            route('admin.berita.kelola'),
            route('admin.laporan.kelola'),
            route('admin.pengguna.kelola'),
            route('admin.program-kerja.kelola'),
            route('admin.event.kelola'),
            route('admin.polling.kelola'),
            route('admin.profil-osis.edit'),
        ] as $url) {
            $this->actingAs($guru)->get($url)->assertOk();
        }
    }

    public function test_admin_dapat_membuka_seluruh_panel_pengelola(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([
            route('admin.beranda'),
            route('admin.pengumuman.kelola'),
            route('admin.berita.kelola'),
            route('admin.laporan.kelola'),
            route('admin.pengguna.kelola'),
            route('admin.program-kerja.kelola'),
            route('admin.event.kelola'),
            route('admin.polling.kelola'),
            route('admin.profil-osis.edit'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_halaman_inti_dapat_diakses_siswa(): void
    {
        $siswa = User::factory()->siswa()->create();

        foreach ([
            route('dashboard'),
            route('feed'),
            route('cari'),
            route('pengumuman.index'),
            route('berita.index'),
            route('notifikasi.index'),
            route('profil.edit'),
        ] as $url) {
            $this->actingAs($siswa)->get($url)->assertOk();
        }
    }

    public function test_profil_publik_siswa_lain_dapat_dilihat(): void
    {
        $siswa = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('profil.show', $lain))
            ->assertOk()
            ->assertSee($lain->name);
    }
}
