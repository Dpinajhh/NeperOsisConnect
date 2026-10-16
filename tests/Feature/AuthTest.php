<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_masuk_tampil_untuk_tamu(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_masuk_dengan_email_berhasil(): void
    {
        $user = User::factory()->siswa()->create(['email' => 'siswa@osisconnect.test']);

        $this->post(route('login.store'), [
            'login' => 'siswa@osisconnect.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_masuk_dengan_nis_tidak_peka_huruf_besar_kecil(): void
    {
        $user = User::factory()->siswa()->create(['nis' => 'SW001']);

        $this->post(route('login.store'), [
            'login' => 'sw001',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_masuk_mencatat_waktu_login_terakhir(): void
    {
        $user = User::factory()->siswa()->create();

        $this->assertNull($user->last_login_at);

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_masuk_gagal_dengan_sandi_salah(): void
    {
        User::factory()->siswa()->create(['email' => 'siswa@osisconnect.test']);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'login' => 'siswa@osisconnect.test',
                'password' => 'sandi-salah',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_pengguna_dapat_keluar(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_pendaftaran_membuat_siswa_dan_langsung_masuk(): void
    {
        $kelas = Kelas::factory()->create();

        $this->post(route('register.store'), [
            'name' => 'Siswa Baru',
            'email' => 'Siswa.Baru@osisconnect.test',
            'nis' => 'sw999',
            'kelas_id' => $kelas->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'syarat' => '1',
        ])->assertRedirect(route('dashboard'));

        // Email di-lowercase, NIS di-uppercase, peran selalu siswa
        $this->assertDatabaseHas('users', [
            'email' => 'siswa.baru@osisconnect.test',
            'nis' => 'SW999',
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $this->assertAuthenticated();
    }

    public function test_pendaftaran_butuh_persetujuan_aturan(): void
    {
        $kelas = Kelas::factory()->create();

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Siswa Baru',
                'email' => 'tanpa.syarat@osisconnect.test',
                'nis' => 'SW998',
                'kelas_id' => $kelas->id,
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertSessionHasErrors('syarat');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'tanpa.syarat@osisconnect.test']);
    }

    public function test_email_dan_nis_duplikat_ditolak(): void
    {
        $kelas = Kelas::factory()->create();
        User::factory()->siswa()->create([
            'email' => 'sudah.ada@osisconnect.test',
            'nis' => 'SW777',
        ]);

        $this->post(route('register.store'), [
            'name' => 'Siswa Lain',
            'email' => 'sudah.ada@osisconnect.test',
            'nis' => 'SW776',
            'kelas_id' => $kelas->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'syarat' => '1',
        ])->assertSessionHasErrors('email');

        $this->post(route('register.store'), [
            'name' => 'Siswa Lain',
            'email' => 'lain@osisconnect.test',
            'nis' => 'SW777',
            'kelas_id' => $kelas->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'syarat' => '1',
        ])->assertSessionHasErrors('nis');
    }

    public function test_akun_diblokir_otomatis_dikeluarkan(): void
    {
        $user = User::factory()->siswa()->create(['is_banned' => true]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_akun_nonaktif_otomatis_dikeluarkan(): void
    {
        $user = User::factory()->siswa()->create(['is_active' => false]);

        $this->actingAs($user)
            ->get(route('feed'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        foreach ([route('dashboard'), route('feed'), route('profil.edit'), route('notifikasi.index')] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_pengguna_yang_sudah_masuk_tidak_bisa_buka_halaman_masuk(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }
}
