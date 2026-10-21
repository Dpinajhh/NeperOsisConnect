<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Post;
use App\Models\Topik;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProduksiSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Jalur seeding produksi: hanya data acuan + admin dari config,
 * tanpa akun uji berpassword "password" maupun konten demo.
 */
class ProduksiSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_produksi_seeder_hanya_mengisi_data_acuan(): void
    {
        config(['osis.admin.email' => null, 'osis.admin.password' => null]);

        $this->seed(ProduksiSeeder::class);

        $this->assertGreaterThan(0, Jurusan::count());
        $this->assertGreaterThan(0, Kelas::count());
        $this->assertGreaterThan(0, Kategori::count());
        $this->assertGreaterThan(0, Topik::count());

        // Tidak ada akun uji maupun pengguna random
        $this->assertSame(0, User::count());
    }

    public function test_produksi_seeder_membuat_admin_dari_config(): void
    {
        config([
            'osis.admin.name' => 'Admin Sekolah',
            'osis.admin.email' => 'admin@sekolah.sch.id',
            'osis.admin.password' => 'sandi-awal-aman',
        ]);

        $this->seed(ProduksiSeeder::class);

        $admin = User::where('email', 'admin@sekolah.sch.id')->first();

        $this->assertNotNull($admin);
        $this->assertSame('Admin Sekolah', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertFalse($admin->is_banned);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('sandi-awal-aman', $admin->password));
    }

    public function test_produksi_seeder_menolak_sandi_terlalu_pendek(): void
    {
        config([
            'osis.admin.email' => 'admin@sekolah.sch.id',
            'osis.admin.password' => 'pendek',
        ]);

        $this->seed(ProduksiSeeder::class);

        $this->assertSame(0, User::where('email', 'admin@sekolah.sch.id')->count());
    }

    public function test_produksi_seeder_aman_dijalankan_dua_kali(): void
    {
        config([
            'osis.admin.email' => 'admin@sekolah.sch.id',
            'osis.admin.password' => 'sandi-awal-aman',
        ]);

        $this->seed(ProduksiSeeder::class);
        $jumlahJurusan = Jurusan::count();

        $this->seed(ProduksiSeeder::class);

        $this->assertSame(1, User::where('email', 'admin@sekolah.sch.id')->count());
        $this->assertSame($jumlahJurusan, Jurusan::count());
    }

    public function test_database_seeder_di_produksi_hanya_data_acuan(): void
    {
        $this->app['env'] = 'production';
        config(['osis.admin.email' => null, 'osis.admin.password' => null]);

        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
            ->assertExitCode(0);

        $this->assertGreaterThan(0, Kelas::count());
        $this->assertSame(0, User::count());
        $this->assertSame(0, Post::count());
    }

    public function test_user_seeder_menolak_berjalan_di_produksi(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--class' => UserSeeder::class, '--force' => true])
            ->assertExitCode(0);

        $this->assertSame(0, User::count());
    }
}
