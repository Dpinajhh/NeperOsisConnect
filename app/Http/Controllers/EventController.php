<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ProgramKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    /** Daftar kegiatan & event sekolah (publik). */
    public function index(): View
    {
        $events = Event::terbit()->mendatang()
            ->withCount('peserta')
            ->get();

        return view('event', compact('events'));
    }

    public function show(Event $event): View
    {
        abort_unless($event->status === 'terbit' || auth()->user()?->isPengelola(), 404);

        $event->loadCount('peserta');
        $event->load(['peserta' => fn ($q) => $q->orderBy('event_peserta.created_at', 'desc')->limit(20)]);
        $sudahDaftar = $event->peserta()->where('users.id', auth()->id())->exists();

        return view('event-detail', compact('event', 'sudahDaftar'));
    }

    // ─── Pendaftaran peserta ───────────────────────────

    public function daftar(Event $event): RedirectResponse
    {
        abort_unless($event->status === 'terbit', 404);

        if ($event->penuh()) {
            return back()->with('error', 'Maaf, kuota peserta event ini sudah penuh.');
        }

        $event->peserta()->syncWithoutDetaching([auth()->id()]);

        return back()->with('sukses', 'Kamu terdaftar pada kegiatan "'.$event->nama.'".');
    }

    public function batal(Event $event): RedirectResponse
    {
        $event->peserta()->detach(auth()->id());

        return back()->with('sukses', 'Pendaftaran kegiatan dibatalkan.');
    }

    // ─── Kelola (admin & guru) ─────────────────────────

    public function kelola(Request $request): View
    {
        $events = Event::withCount('peserta')->latest('waktu_mulai')->get();
        $programs = ProgramKerja::orderBy('nama')->get();

        // Baris yang sedang diedit (via ?edit=id)
        $edit = $request->filled('edit') ? Event::find($request->integer('edit')) : null;

        return view('admin.event', compact('events', 'programs', 'edit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        if ($request->hasFile('poster') && $request->file('poster')->isValid()) {
            $validated['poster'] = $request->file('poster')->store('event', 'public');
        }

        Event::create([
            ...$validated,
            'slug' => $this->slugUnik($validated['nama']),
            'user_id' => $request->user()->id,
        ]);

        return back()->with('sukses', 'Kegiatan dibuat.');
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $this->validasi($request);

        if ($request->hasFile('poster') && $request->file('poster')->isValid()) {
            $validated['poster'] = $request->file('poster')->store('event', 'public');
        }

        $validated['slug'] = $event->nama === $validated['nama']
            ? $event->slug
            : $this->slugUnik($validated['nama']);

        $event->update($validated);

        return back()->with('sukses', 'Kegiatan diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return back()->with('sukses', 'Kegiatan dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'waktu_mulai' => ['required', 'date'],
            'waktu_selesai' => ['nullable', 'date', 'after_or_equal:waktu_mulai'],
            'kuota' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:draft,terbit'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'program_kerja_id' => ['nullable', 'exists:program_kerja,id'],
        ], [
            'nama.required' => 'Nama kegiatan wajib diisi.',
            'deskripsi.required' => 'Deskripsi kegiatan wajib diisi.',
            'waktu_mulai.required' => 'Waktu pelaksanaan wajib diisi.',
            'waktu_selesai.after_or_equal' => 'Waktu selesai harus setelah waktu mulai.',
            'poster.image' => 'Poster harus berupa gambar.',
            'poster.max' => 'Ukuran poster maksimal 2 MB.',
        ]);
    }

    private function slugUnik(string $nama): string
    {
        $dasar = Str::slug($nama);
        $slug = $dasar;
        $i = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $dasar.'-'.++$i;
        }

        return $slug;
    }
}
