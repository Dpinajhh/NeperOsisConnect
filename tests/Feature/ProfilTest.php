<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Edit profil mandiri: data diri & avatar, plus ganti kata sandi.
 * Data identitas (email, NIS, kelas, peran) tidak boleh diubah dari sini.
 */
class ProfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_edit_menampilkan_data_pengguna(): void
    {
        $user = User::factory()->siswa()->create(['name' => 'Rani Siswa']);

        $this->actingAs($user)
            ->get(route('profil.edit'))
            ->assertOk()
            ->assertSee('Rani Siswa')
            ->assertSee($user->email)
            ->assertSee('Ganti Kata Sandi');
    }

    public function test_pengguna_dapat_memperbarui_data_diri(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.update'), [
                'name' => 'Nama Baru',
                'bio' => 'Halo, ini bio baru.',
                'telepon' => '081234567890',
            ])
            ->assertRedirect()
            ->assertSessionHas('sukses');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('Halo, ini bio baru.', $user->bio);
        $this->assertSame('081234567890', $user->telepon);
    }

    public function test_avatar_dapat_diunggah_dan_berkas_lama_dihapus(): void
    {
        Storage::fake('public');
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.update'), [
                'name' => $user->name,
                'avatar' => UploadedFile::fake()->image('aku.jpg', 400, 400),
            ])
            ->assertRedirect();

        $pertama = $user->fresh()->avatar;
        $this->assertNotNull($pertama);
        Storage::disk('public')->assertExists($pertama);

        // Unggah lagi: berkas lama harus diganti, bukan menumpuk
        $this->actingAs($user)
            ->put(route('profil.update'), [
                'name' => $user->name,
                'avatar' => UploadedFile::fake()->image('aku2.png', 300, 300),
            ]);

        $kedua = $user->fresh()->avatar;
        $this->assertNotSame($pertama, $kedua);
        Storage::disk('public')->assertMissing($pertama);
        Storage::disk('public')->assertExists($kedua);
    }

    public function test_nama_wajib_diisi(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.update'), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_berkas_bukan_gambar_ditolak(): void
    {
        Storage::fake('public');
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.update'), [
                'name' => $user->name,
                'avatar' => UploadedFile::fake()->create('jahat.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('avatar');

        $this->assertNull($user->fresh()->avatar);
    }

    public function test_data_identitas_tidak_dapat_diubah_dari_profil(): void
    {
        $user = User::factory()->siswa()->create([
            'email' => 'asli@osisconnect.test',
            'nis' => '9001',
        ]);

        $this->actingAs($user)
            ->put(route('profil.update'), [
                'name' => 'Coba Curang',
                'email' => 'ganti@jahat.test',
                'nis' => '0000',
                'role' => 'admin',
                'is_active' => false,
                'is_banned' => false,
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertSame('asli@osisconnect.test', $user->email);
        $this->assertSame('9001', $user->nis);
        $this->assertSame('siswa', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_ganti_sandi_berhasil_bila_sandi_lama_benar(): void
    {
        $user = User::factory()->siswa()->create(); // sandi bawaan: 'password'

        $this->actingAs($user)
            ->put(route('profil.sandi'), [
                'sandi_lama' => 'password',
                'sandi' => 'rahasia-baru-123',
                'sandi_confirmation' => 'rahasia-baru-123',
            ])
            ->assertRedirect()
            ->assertSessionHas('sukses');

        $this->assertTrue(Hash::check('rahasia-baru-123', $user->fresh()->password));
    }

    public function test_ganti_sandi_gagal_bila_sandi_lama_salah(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.sandi'), [
                'sandi_lama' => 'salah-sekali',
                'sandi' => 'rahasia-baru-123',
                'sandi_confirmation' => 'rahasia-baru-123',
            ])
            ->assertSessionHasErrors('sandi_lama');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_konfirmasi_sandi_baru_harus_sama(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.sandi'), [
                'sandi_lama' => 'password',
                'sandi' => 'rahasia-baru-123',
                'sandi_confirmation' => 'beda-dong-456',
            ])
            ->assertSessionHasErrors('sandi');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_sandi_baru_minimal_8_karakter(): void
    {
        $user = User::factory()->siswa()->create();

        $this->actingAs($user)
            ->put(route('profil.sandi'), [
                'sandi_lama' => 'password',
                'sandi' => 'pendek',
                'sandi_confirmation' => 'pendek',
            ])
            ->assertSessionHasErrors('sandi');
    }

    public function test_tamu_tidak_dapat_mengakses_edit_profil(): void
    {
        $this->get(route('profil.edit'))->assertRedirect(route('login'));
        $this->put(route('profil.update'), ['name' => 'X'])->assertRedirect(route('login'));
        $this->put(route('profil.sandi'), ['sandi_lama' => 'x'])->assertRedirect(route('login'));
    }
}
