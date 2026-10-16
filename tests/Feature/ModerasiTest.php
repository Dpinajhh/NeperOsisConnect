<?php

namespace Tests\Feature;

use App\Models\Laporan;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pelaporan konten oleh siswa & tindak lanjut moderasi oleh pengelola.
 */
class ModerasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_dapat_melaporkan_postingan_orang_lain(): void
    {
        $pelapor = User::factory()->siswa()->create();
        $pemilik = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $pemilik->id]);

        $this->actingAs($pelapor)
            ->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => 'Konten tidak pantas untuk siswa.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('laporan', [
            'pelapor_id' => $pelapor->id,
            'laporanable_type' => Post::class,
            'laporanable_id' => $post->id,
            'status' => 'open',
        ]);
    }

    public function test_siswa_tidak_dapat_melaporkan_konten_sendiri(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)
            ->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => 'Coba lapor diri sendiri.',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('laporan', 0);
    }

    public function test_laporan_duplikat_ditolak(): void
    {
        $pelapor = User::factory()->siswa()->create();
        $post = Post::factory()->create();

        $payload = [
            'tipe' => 'post',
            'id' => $post->id,
            'alasan' => 'Sudah pernah dilaporkan.',
        ];

        $this->actingAs($pelapor)->post(route('lapor.store'), $payload);
        $this->actingAs($pelapor)->post(route('lapor.store'), $payload)->assertSessionHas('error');

        $this->assertDatabaseCount('laporan', 1);
    }

    public function test_alasan_wajib_diisi(): void
    {
        $pelapor = User::factory()->siswa()->create();
        $post = Post::factory()->create();

        $this->actingAs($pelapor)
            ->post(route('lapor.store'), [
                'tipe' => 'post',
                'id' => $post->id,
                'alasan' => '',
            ])
            ->assertSessionHasErrors('alasan');

        $this->assertDatabaseCount('laporan', 0);
    }

    public function test_tipe_konten_tidak_dikenal_ditolak(): void
    {
        $pelapor = User::factory()->siswa()->create();

        $this->actingAs($pelapor)
            ->post(route('lapor.store'), [
                'tipe' => 'video',
                'id' => 1,
                'alasan' => 'Tipe aneh.',
            ])
            ->assertSessionHasErrors('tipe');
    }

    public function test_tamu_tidak_bisa_melaporkan(): void
    {
        $post = Post::factory()->create();

        $this->post(route('lapor.store'), [
            'tipe' => 'post',
            'id' => $post->id,
            'alasan' => 'Tanpa login.',
        ])->assertRedirect(route('login'));
    }

    public function test_admin_dapat_menandai_laporan_sedang_diproses(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['status' => 'open']);

        $this->actingAs($admin)
            ->post(route('admin.laporan.perbarui', $laporan), [
                'status' => 'diproses',
                'catatan_admin' => 'Sedang ditinjau.',
            ])
            ->assertRedirect();

        $laporan->refresh();
        $this->assertSame('diproses', $laporan->status);
        $this->assertSame($admin->id, $laporan->ditangani_oleh);
    }

    public function test_admin_dapat_menolak_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['status' => 'open']);

        $this->actingAs($admin)
            ->post(route('admin.laporan.perbarui', $laporan), ['status' => 'ditolak'])
            ->assertRedirect();

        $this->assertSame('ditolak', $laporan->fresh()->status);
    }

    public function test_status_laporan_tidak_valid_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        $laporan = Laporan::factory()->create(['status' => 'open']);

        $this->actingAs($admin)
            ->post(route('admin.laporan.perbarui', $laporan), ['status' => 'entahlah'])
            ->assertSessionHasErrors('status');

        $this->assertSame('open', $laporan->fresh()->status);
    }

    public function test_admin_dapat_menghapus_konten_yang_dilaporkan(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();
        $laporan = Laporan::factory()->create([
            'laporanable_type' => Post::class,
            'laporanable_id' => $post->id,
            'status' => 'diproses',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.laporan.hapus-konten', $laporan))
            ->assertRedirect();

        // Post memakai SoftDeletes
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
        $this->assertSame('selesai', $laporan->fresh()->status);
    }

    public function test_siswa_tidak_boleh_membuka_halaman_moderasi(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('admin.laporan.kelola'))
            ->assertForbidden();
    }

    public function test_siswa_tidak_boleh_menghapus_konten_lewat_moderasi(): void
    {
        $siswa = User::factory()->siswa()->create();
        $post = Post::factory()->create();
        $laporan = Laporan::factory()->create([
            'laporanable_type' => Post::class,
            'laporanable_id' => $post->id,
        ]);

        $this->actingAs($siswa)
            ->post(route('admin.laporan.hapus-konten', $laporan))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_guru_boleh_menangani_laporan(): void
    {
        $guru = User::factory()->guru()->create();
        $laporan = Laporan::factory()->create(['status' => 'open']);

        $this->actingAs($guru)
            ->get(route('admin.laporan.kelola'))
            ->assertOk();
    }
}
