<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan seeder tetap menetapkan peran & status akun dengan benar
 * setelah kolom role/is_active dikeluarkan dari daftar fillable.
 */
class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_membuat_akun_uji_dengan_peran_dan_status_benar(): void
    {
        $this->seed(UserSeeder::class);

        $admin = User::where('email', 'admin@osisconnect.test')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertNotNull($admin->email_verified_at);

        $guru = User::where('email', 'guru@osisconnect.test')->firstOrFail();
        $this->assertSame('guru', $guru->role);
        $this->assertTrue($guru->is_active);
        $this->assertNotNull($guru->email_verified_at);

        $siswa = User::where('email', 'siswa@osisconnect.test')->firstOrFail();
        $this->assertSame('siswa', $siswa->role);
        $this->assertTrue($siswa->is_active);
        $this->assertNotNull($siswa->email_verified_at);

        // 3 akun uji + 1 admin + 2 guru + 40 siswa dari factory
        $this->assertSame(46, User::count());
        $this->assertSame(2, User::where('role', 'admin')->count());
        $this->assertSame(3, User::where('role', 'guru')->count());
        $this->assertSame(41, User::where('role', 'siswa')->count());
    }

    public function test_akun_uji_dapat_masuk_setelah_di_seed(): void
    {
        $this->seed(UserSeeder::class);

        foreach (['admin', 'guru', 'siswa'] as $peran) {
            $this->post(route('login.store'), [
                'login' => "{$peran}@osisconnect.test",
                'password' => 'password',
            ])->assertRedirect(route('dashboard'));

            $this->assertAuthenticated();
            $this->post(route('logout'));
        }
    }

    public function test_seeder_aman_dijalankan_dua_kali(): void
    {
        $this->seed(UserSeeder::class);
        $jumlahPertama = User::count();

        $this->seed(UserSeeder::class);

        // updateOrCreate tidak menggandakan akun uji utama
        $this->assertSame(1, User::where('email', 'admin@osisconnect.test')->count());
        $this->assertSame(1, User::where('email', 'guru@osisconnect.test')->count());
        $this->assertSame(1, User::where('email', 'siswa@osisconnect.test')->count());

        // Peran akun uji tetap benar setelah seed kedua
        $this->assertSame('admin', User::where('email', 'admin@osisconnect.test')->first()->role);
        $this->assertSame('guru', User::where('email', 'guru@osisconnect.test')->first()->role);
        $this->assertGreaterThanOrEqual($jumlahPertama, User::count());
    }
}
