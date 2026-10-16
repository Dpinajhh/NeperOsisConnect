<?php

namespace Tests\Feature;

use App\Models\Polling;
use App\Models\PilihanPolling;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Polling siswa: satu suara per pengguna per polling.
 */
class PollingTest extends TestCase
{
    use RefreshDatabase;

    private function buatPolling(string $status = 'terbit'): array
    {
        $pembuat = User::factory()->admin()->create();

        $polling = Polling::create([
            'pertanyaan' => 'Jam ekstrakurikuler yang paling pas?',
            'slug' => 'jam-ekstrakurikuler-'.uniqid(),
            'status' => $status,
            'user_id' => $pembuat->id,
        ]);

        $pilihanA = PilihanPolling::create([
            'polling_id' => $polling->id,
            'teks' => 'Sepulang sekolah',
            'urutan' => 1,
        ]);

        $pilihanB = PilihanPolling::create([
            'polling_id' => $polling->id,
            'teks' => 'Hari Sabtu',
            'urutan' => 2,
        ]);

        return [$polling, $pilihanA, $pilihanB];
    }

    public function test_siswa_dapat_memberikan_suara(): void
    {
        [$polling, $pilihanA] = $this->buatPolling();
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->post(route('polling.vote', $polling), ['pilihan_id' => $pilihanA->id])
            ->assertRedirect(route('polling.detail', $polling));

        $this->assertDatabaseHas('polling_suara', [
            'polling_id' => $polling->id,
            'pilihan_id' => $pilihanA->id,
            'user_id' => $siswa->id,
        ]);
    }

    public function test_satu_siswa_hanya_boleh_memilih_sekali(): void
    {
        [$polling, $pilihanA, $pilihanB] = $this->buatPolling();
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->post(route('polling.vote', $polling), ['pilihan_id' => $pilihanA->id]);

        // Percobaan kedua dengan pilihan berbeda harus ditolak
        $this->actingAs($siswa)
            ->post(route('polling.vote', $polling), ['pilihan_id' => $pilihanB->id])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('polling_suara', 1);
        $this->assertDatabaseHas('polling_suara', ['pilihan_id' => $pilihanA->id]);
    }

    public function test_polling_ditutup_tidak_menerima_suara(): void
    {
        [$polling, $pilihanA] = $this->buatPolling('ditutup');
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->post(route('polling.vote', $polling), ['pilihan_id' => $pilihanA->id])
            ->assertNotFound();

        $this->assertDatabaseCount('polling_suara', 0);
    }

    public function test_polling_draft_tidak_tampil_di_daftar(): void
    {
        [$draft] = $this->buatPolling('draft');
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->get(route('polling'))
            ->assertDontSee($draft->pertanyaan);
    }

    public function test_pilihan_dari_polling_lain_ditolak(): void
    {
        [$polling, $pilihanA] = $this->buatPolling();
        [, , $pilihanLain] = $this->buatPolling();

        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)
            ->post(route('polling.vote', $polling), ['pilihan_id' => $pilihanLain->id])
            ->assertNotFound();

        $this->assertDatabaseCount('polling_suara', 0);
    }

    public function test_tamu_tidak_dapat_memberi_suara(): void
    {
        [$polling, $pilihanA] = $this->buatPolling();

        $this->post(route('polling.vote', $polling), ['pilihan_id' => $pilihanA->id])
            ->assertRedirect(route('login'));
    }
}
