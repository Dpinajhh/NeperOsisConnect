<?php

namespace App\Http\Controllers;

use App\Models\Polling;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PollingController extends Controller
{
    /** Daftar polling aktif (publik). */
    public function index(): View
    {
        $polling = Polling::aktif()->withCount('suara')->latest()->get();

        return view('polling', compact('polling'));
    }

    /** Detail polling: form voting atau hasil bila sudah memilih. */
    public function show(Polling $polling): View
    {
        abort_unless($polling->status === 'terbit', 404, 'Polling tidak tersedia.');

        $polling->load('pilihan');
        $user = auth()->user();

        $sudahMemilih = $polling->sudahMemilih($user?->id);
        $pilihanSaya = $polling->suara()->where('user_id', $user?->id)->value('pilihan_id');

        return view('polling-detail', [
            'polling' => $polling,
            'sudahMemilih' => $sudahMemilih,
            'pilihanSaya' => $pilihanSaya,
            'hasil' => $sudahMemilih ? $polling->hasil() : null,
            'totalSuara' => $polling->totalSuara(),
        ]);
    }

    /** Simpan suara — satu pengguna hanya sekali per polling. */
    public function vote(Request $request, Polling $polling): RedirectResponse
    {
        abort_unless($polling->status === 'terbit', 404);

        $validated = $request->validate([
            'pilihan_id' => ['required', 'exists:polling_pilihan,id'],
        ], [
            'pilihan_id.required' => 'Pilih salah satu opsi dulu.',
        ]);

        $pilihan = $polling->pilihan()->whereKey($validated['pilihan_id'])->firstOrFail();

        if ($polling->sudahMemilih(auth()->id())) {
            return back()->with('error', 'Kamu sudah memberikan suara pada polling ini.');
        }

        $polling->suara()->create([
            'pilihan_id' => $pilihan->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('polling.detail', $polling)
            ->with('sukses', 'Terima kasih, suaramu sudah tercatat!');
    }

    // ─── Kelola (admin & guru) ────────────────────────────

    public function kelola(): View
    {
        $polling = Polling::withCount('suara')->latest()->get();

        return view('admin.polling', compact('polling'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        $polling = Polling::create([
            'pertanyaan' => $validated['pertanyaan'],
            'slug' => $this->slugUnik($validated['pertanyaan']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
            'user_id' => $request->user()->id,
        ]);

        $this->simpanPilihan($polling, $validated['pilihan']);

        return back()->with('sukses', 'Polling dibuat.');
    }

    public function update(Request $request, Polling $polling): RedirectResponse
    {
        $validated = $this->validasi($request);

        // Pilihan hanya bisa diubah jika belum ada suara masuk
        if ($polling->totalSuara() > 0) {
            return back()->with('error', 'Polling yang sudah menerima suara tidak dapat diubah pilihannya.');
        }

        $polling->update([
            'pertanyaan' => $validated['pertanyaan'],
            'slug' => $polling->pertanyaan === $validated['pertanyaan'] ? $polling->slug : $this->slugUnik($validated['pertanyaan']),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $validated['status'],
        ]);

        $polling->pilihan()->delete();
        $this->simpanPilihan($polling, $validated['pilihan']);

        return back()->with('sukses', 'Polling diperbarui.');
    }

    public function tutup(Polling $polling): RedirectResponse
    {
        $polling->update(['status' => $polling->status === 'ditutup' ? 'terbit' : 'ditutup']);

        return back()->with('sukses', $polling->status === 'ditutup'
            ? 'Polling ditutup, suara tidak lagi diterima.'
            : 'Polling dibuka kembali.');
    }

    public function destroy(Polling $polling): RedirectResponse
    {
        $polling->delete();

        return back()->with('sukses', 'Polling dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'pertanyaan' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,terbit,ditutup'],
            'pilihan' => ['required', 'array', 'min:2'],
            'pilihan.*' => ['required', 'string', 'max:200', 'distinct'],
        ], [
            'pertanyaan.required' => 'Pertanyaan polling wajib diisi.',
            'pilihan.required' => 'Minimal dua pilihan jawaban.',
            'pilihan.min' => 'Polling harus punya minimal dua pilihan.',
            'pilihan.*.distinct' => 'Pilihan jawaban tidak boleh sama.',
        ]);
    }

    private function simpanPilihan(Polling $polling, array $pilihan): void
    {
        foreach (array_values($pilihan) as $i => $teks) {
            $polling->pilihan()->create([
                'teks' => $teks,
                'urutan' => $i + 1,
            ]);
        }
    }

    private function slugUnik(string $pertanyaan): string
    {
        $dasar = Str::slug(Str::limit($pertanyaan, 60, ''));
        $slug = $dasar;
        $i = 1;

        while (Polling::where('slug', $slug)->exists()) {
            $slug = $dasar.'-'.++$i;
        }

        return $slug;
    }
}
