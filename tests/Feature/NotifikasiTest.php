<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Notifikasi: hanya milik sendiri, bisa ditandai dibaca, dan
 * arahnya tidak boleh keluar dari aplikasi (anti open redirect).
 */
class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatNotifikasi(User $user, array $atribut = []): Notifikasi
    {
        return Notifikasi::create(array_merge([
            'user_id' => $user->id,
            'tipe' => 'suka',
            'judul' => 'Suka baru',
            'pesan' => 'Seseorang menyukai postinganmu.',
            'url' => '/feed',
        ], $atribut));
    }

    public function test_daftar_hanya_menampilkan_notifikasi_milik_sendiri(): void
    {
        $saya = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create();

        $this->buatNotifikasi($saya, ['judul' => 'Notif milik saya']);
        $this->buatNotifikasi($lain, ['judul' => 'Notif orang lain']);

        $this->actingAs($saya)
            ->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee('Notif milik saya')
            ->assertDontSee('Notif orang lain');
    }

    public function test_filter_belum_dibaca_menyembunyikan_yang_sudah_dibaca(): void
    {
        $saya = User::factory()->siswa()->create();

        $this->buatNotifikasi($saya, ['judul' => 'Masih baru']);
        $sudah = $this->buatNotifikasi($saya, ['judul' => 'Sudah lama']);
        $sudah->tandaiDibaca();

        $this->actingAs($saya)
            ->get(route('notifikasi.index', ['filter' => 'belum']))
            ->assertOk()
            ->assertSee('Masih baru')
            ->assertDontSee('Sudah lama');
    }

    public function test_membaca_notifikasi_menandai_dibaca_dan_mengantar_ke_tujuan(): void
    {
        $saya = User::factory()->siswa()->create();
        $notif = $this->buatNotifikasi($saya, ['url' => '/feed']);

        $this->actingAs($saya)
            ->post(route('notifikasi.baca', $notif))
            ->assertRedirect('/feed');

        $this->assertNotNull($notif->fresh()->dibaca_at);
    }

    public function test_tidak_bisa_membaca_notifikasi_orang_lain(): void
    {
        $saya = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create();
        $notif = $this->buatNotifikasi($lain);

        $this->actingAs($saya)
            ->post(route('notifikasi.baca', $notif))
            ->assertForbidden();

        $this->assertNull($notif->fresh()->dibaca_at);
    }

    public function test_tandai_semua_dibaca_hanya_menyentuh_milik_sendiri(): void
    {
        $saya = User::factory()->siswa()->create();
        $lain = User::factory()->siswa()->create();

        $this->buatNotifikasi($saya);
        $this->buatNotifikasi($saya);
        $notifLain = $this->buatNotifikasi($lain);

        $this->actingAs($saya)
            ->post(route('notifikasi.baca-semua'))
            ->assertRedirect();

        $this->assertSame(0, $saya->notifikasi()->belumDibaca()->count());
        $this->assertNull($notifLain->fresh()->dibaca_at);
    }

    public function test_tujuan_di_luar_aplikasi_tidak_diikuti(): void
    {
        $saya = User::factory()->siswa()->create();
        $notif = $this->buatNotifikasi($saya, ['url' => 'https://situs-jahat.example/pancing']);

        $this->from(route('notifikasi.index'))
            ->actingAs($saya)
            ->post(route('notifikasi.baca', $notif))
            ->assertRedirect(route('notifikasi.index'));

        // Tetap ditandai dibaca, hanya arahnya yang diabaikan
        $this->assertNotNull($notif->fresh()->dibaca_at);
    }

    public function test_tujuan_protokol_relatif_dan_backslash_tidak_diikuti(): void
    {
        $saya = User::factory()->siswa()->create();

        foreach (['//situs-jahat.example/pancing', '/\\situs-jahat.example/pancing'] as $url) {
            $notif = $this->buatNotifikasi($saya, ['url' => $url]);

            $this->from(route('notifikasi.index'))
                ->actingAs($saya)
                ->post(route('notifikasi.baca', $notif))
                ->assertRedirect(route('notifikasi.index'));
        }
    }

    public function test_tamu_tidak_dapat_membuka_notifikasi(): void
    {
        $this->get(route('notifikasi.index'))->assertRedirect(route('login'));
        $this->post(route('notifikasi.baca-semua'))->assertRedirect(route('login'));
    }
}
