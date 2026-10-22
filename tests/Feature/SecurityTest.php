<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Komentar;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Penjagaan keamanan: mass assignment, rate limit, header, dan privasi data.
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_tautan_reset_tidak_mengikuti_host_palsu(): void
    {
        Notification::fake();

        $user = User::factory()->siswa()->create([
            'email' => 'korban@osisconnect.test',
        ]);

        $this->post('http://evil.example/lupa-sandi', [
            'email' => $user->email,
        ])->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notifikasi) use ($user): bool {
            $url = $notifikasi->toMail($user)->actionUrl;

            return str_starts_with($url, rtrim((string) config('app.url'), '/'))
                && ! str_contains($url, 'evil.example');
        });
    }

    // ─── Mass assignment: kolom istimewa tidak boleh terisi dari input ───

    public function test_role_tidak_dapat_diubah_lewat_mass_assignment(): void
    {
        $siswa = User::factory()->siswa()->create();

        $siswa->fill(['role' => 'admin'])->save();

        $this->assertSame('siswa', $siswa->fresh()->role);
    }

    public function test_role_aman_saat_dibuat_lewat_create_dengan_input_mentah(): void
    {
        // Meniru pola berbahaya: User::create($request->all())
        $user = User::create([
            'name' => 'Penyusup',
            'email' => 'penyusup@osisconnect.test',
            'password' => 'rahasia123',
            'role' => 'admin',
            'is_active' => false,
            'is_banned' => true,
        ]);

        // Ketiganya diabaikan; kolom memakai nilai default database
        $this->assertSame('siswa', $user->fresh()->role);
        $this->assertTrue($user->fresh()->is_active);
        $this->assertFalse($user->fresh()->is_banned);
    }

    public function test_status_aktif_dan_blokir_tidak_dapat_diisi_massal(): void
    {
        $user = User::factory()->siswa()->create([
            'is_active' => false,
            'is_banned' => true,
        ]);

        $user->fill(['is_active' => true, 'is_banned' => false])->save();

        $this->assertFalse($user->fresh()->is_active);
        $this->assertTrue($user->fresh()->is_banned);
    }

    public function test_last_login_tidak_dapat_diisi_massal(): void
    {
        $user = User::factory()->siswa()->create(['last_login_at' => null]);

        $user->fill(['last_login_at' => now()])->save();

        $this->assertNull($user->fresh()->last_login_at);
    }

    // ─── Jalur resmi tetap berfungsi setelah pengetatan ───

    public function test_pendaftaran_membuat_akun_siswa_aktif_dan_terverifikasi(): void
    {
        $kelas = Kelas::factory()->create();

        $this->post(route('register.store'), [
            'name' => 'Siswa Baru',
            'email' => 'baru@osisconnect.test',
            'nis' => 'SW950',
            'kelas_id' => $kelas->id,
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'syarat' => '1',
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', 'baru@osisconnect.test')->firstOrFail();

        $this->assertSame('siswa', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_admin_dapat_mengubah_peran_pengguna(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        $this->actingAs($admin)
            ->post(route('admin.pengguna.peran', $target), ['role' => 'guru'])
            ->assertRedirect();

        $this->assertSame('guru', $target->fresh()->role);
    }

    public function test_admin_dapat_memblokir_lalu_mengaktifkan_kembali(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->siswa()->create();

        // Blokir
        $this->actingAs($admin)->post(route('admin.pengguna.blokir', $target));

        $target->refresh();
        $this->assertTrue($target->is_banned);
        $this->assertFalse($target->is_active);

        // Lepas blokir
        $this->actingAs($admin)->post(route('admin.pengguna.blokir', $target));

        $target->refresh();
        $this->assertFalse($target->is_banned);
        $this->assertTrue($target->is_active);
    }

    public function test_admin_tidak_dapat_menurunkan_peran_dirinya_sendiri(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.pengguna.peran', $admin), ['role' => 'siswa'])
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    // ─── Rate limiting ───

    public function test_pelaporan_dibatasi_sepuluh_per_jam(): void
    {
        $pelapor = User::factory()->siswa()->create();
        $post = Post::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($pelapor)->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => 'Percobaan ke-'.$i,
            ]);
        }

        // Permintaan ke-11 harus ditolak (HTTP 429)
        $this->actingAs($pelapor)
            ->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => 'Percobaan terakhir',
            ])
            ->assertStatus(429);
    }

    public function test_membuat_postingan_dibatasi_per_jam(): void
    {
        $siswa = User::factory()->siswa()->create();

        for ($i = 0; $i < 20; $i++) {
            $this->actingAs($siswa)->post(route('posts.store'), [
                'konten' => 'Postingan uji ke-'.$i,
                'visibilitas' => 'sekolah',
            ]);
        }

        $this->actingAs($siswa)
            ->post(route('posts.store'), [
                'konten' => 'Postingan ke-21',
                'visibilitas' => 'sekolah',
            ])
            ->assertStatus(429);

        $this->assertSame(20, Post::count());
    }

    public function test_suka_dibatasi_seratus_dua_puluh_per_menit(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['visibilitas' => 'sekolah']);

        for ($i = 0; $i < 120; $i++) {
            $this->actingAs($siswa)
                ->post(route('posts.suka', $post))
                ->assertOk();
        }

        $this->actingAs($siswa)
            ->post(route('posts.suka', $post))
            ->assertStatus(429);
    }

    // ─── Header keamanan ───

    public function test_header_keamanan_terpasang_di_respons(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy', "frame-ancestors 'self'");
    }

    public function test_header_keamanan_juga_ada_di_halaman_ber_login(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    // ─── Privasi: waktu login terakhir ───

    public function test_waktu_login_pengguna_lain_tidak_ditampilkan(): void
    {
        $pengunjung = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create(['last_login_at' => now()->subDays(2)]);

        $this->actingAs($pengunjung)
            ->get(route('profil.show', $lain))
            ->assertOk()
            ->assertSee('Bergabung')
            ->assertDontSee('Login terakhir');
    }

    public function test_diri_sendiri_dan_pengelola_masih_melihat_waktu_login(): void
    {
        $siswa = User::factory()->siswa()->create(['last_login_at' => now()->subDay()]);
        $admin = User::factory()->admin()->create();

        // Diri sendiri
        $this->actingAs($siswa)
            ->get(route('profil.show', $siswa))
            ->assertOk()
            ->assertSee('Login terakhir');

        // Pengelola (admin/guru)
        $this->actingAs($admin)
            ->get(route('profil.show', $siswa))
            ->assertOk()
            ->assertSee('Login terakhir');
    }

    // ─── Otorisasi kelola pengguna: hanya admin ───

    public function test_guru_tidak_dapat_mengelola_pengguna_atau_mengubah_peran(): void
    {
        $guru = User::factory()->guru()->create();
        $target = User::factory()->siswa()->create();

        $this->actingAs($guru)
            ->get(route('admin.pengguna.kelola'))
            ->assertForbidden();

        $this->actingAs($guru)
            ->post(route('admin.pengguna.peran', $target), ['role' => 'admin'])
            ->assertForbidden();

        $this->actingAs($guru)
            ->post(route('admin.pengguna.blokir', $target))
            ->assertForbidden();

        $this->assertSame('siswa', $target->fresh()->role);
        $this->assertFalse($target->fresh()->is_banned);
    }

    // ─── Visibilitas interaksi ───

    public function test_suka_pada_post_yang_tidak_terlihat_ditolak(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();
        $pemilik = User::factory()->siswa()->create(['kelas_id' => $kelasA->id]);
        $post = Post::factory()->create([
            'user_id' => $pemilik->id,
            'visibilitas' => 'kelas',
        ]);
        $pengunjung = User::factory()->siswa()->create(['kelas_id' => $kelasB->id]);
        $jumlahAwal = $post->jumlah_suka;

        $this->actingAs($pengunjung)
            ->post(route('posts.suka', $post))
            ->assertForbidden();

        $this->assertDatabaseCount('post_suka', 0);
        $this->assertSame($jumlahAwal, $post->fresh()->jumlah_suka);
    }

    public function test_balasan_harus_berada_pada_post_yang_sama(): void
    {
        $siswa = User::factory()->siswa()->create();
        $postA = Post::factory()->create(['visibilitas' => 'sekolah']);
        $postB = Post::factory()->create(['visibilitas' => 'sekolah']);
        $komentarA = Komentar::factory()->create(['post_id' => $postA->id]);

        $this->actingAs($siswa)
            ->post(route('posts.komentar', $postB), [
                'konten' => 'Balasan lintas post',
                'parent_id' => $komentarA->id,
            ])
            ->assertSessionHasErrors('parent_id');

        $this->assertDatabaseMissing('komentar', [
            'post_id' => $postB->id,
            'parent_id' => $komentarA->id,
        ]);
    }

    public function test_komentar_tidak_dapat_dihapus_lewat_post_lain(): void
    {
        $siswa = User::factory()->siswa()->create();
        $postA = Post::factory()->create(['visibilitas' => 'sekolah']);
        $postB = Post::factory()->create(['visibilitas' => 'sekolah']);
        $komentar = Komentar::factory()->create([
            'post_id' => $postA->id,
            'user_id' => $siswa->id,
        ]);
        $counterB = $postB->jumlah_komentar;

        $this->actingAs($siswa)
            ->delete(route('posts.komentar.hapus', [$postB, $komentar]))
            ->assertNotFound();

        $this->assertNotSoftDeleted('komentar', ['id' => $komentar->id]);
        $this->assertSame($counterB, $postB->fresh()->jumlah_komentar);
    }

    // ─── Pembatas laju per aksi ───

    public function test_jatah_pembatas_laju_terpisah_antar_aksi(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['visibilitas' => 'sekolah']);

        // 11 interaksi suka: masih jauh di bawah batas 120/menit
        for ($i = 0; $i < 11; $i++) {
            $this->actingAs($siswa)->post(route('posts.suka', $post))->assertOk();
        }

        // Aksi lain (lapor 10/jam) tidak boleh ikut terkena 429
        $this->actingAs($siswa)
            ->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => 'Uji pemisahan pembatas laju',
            ])
            ->assertRedirect();
    }

    public function test_siswa_tidak_dapat_melaporkan_post_yang_tidak_terlihat(): void
    {
        $kelasA = Kelas::factory()->create();
        $kelasB = Kelas::factory()->create();
        $pemilik = User::factory()->siswa()->create(['kelas_id' => $kelasA->id]);
        $post = Post::factory()->create([
            'user_id' => $pemilik->id,
            'visibilitas' => 'kelas',
        ]);
        $pelapor = User::factory()->siswa()->create(['kelas_id' => $kelasB->id]);

        $this->actingAs($pelapor)
            ->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => 'Uji laporan post tak terlihat',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('laporan', 0);
    }
}
