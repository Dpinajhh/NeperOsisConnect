<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ProgramKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProgramKerjaController extends Controller
{
    /** Daftar program kerja (publik). */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $programs = ProgramKerja::terbaru()
            ->when($status && in_array($status, ['rencana', 'berjalan', 'selesai', 'batal'], true),
                fn ($q) => $q->where('status', $status))
            ->withCount('kegiatan')
            ->get();

        return view('program-kerja', compact('programs', 'status'));
    }

    /** Detail program kerja + kegiatan terkait. */
    public function show(ProgramKerja $program): View
    {
        $program->loadMissing(['kegiatan' => fn ($q) => $q->terbit()->mendatang()]);

        return view('program-kerja-detail', compact('program'));
    }

    // ─── Kelola (admin & guru) ─────────────────────────

    public function kelola(Request $request): View
    {
        $programs = ProgramKerja::terbaru()->withCount('kegiatan')->get();

        // Baris yang sedang diedit (via ?edit=id)
        $edit = $request->filled('edit') ? ProgramKerja::find($request->integer('edit')) : null;

        return view('admin.program-kerja', compact('programs', 'edit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        ProgramKerja::create([
            ...$validated,
            'slug' => $this->slugUnik($validated['nama']),
            'user_id' => $request->user()->id,
        ]);

        return back()->with('sukses', 'Program kerja ditambahkan.');
    }

    public function update(Request $request, ProgramKerja $program): RedirectResponse
    {
        $program->update($this->validasi($request));

        return back()->with('sukses', 'Program kerja diperbarui.');
    }

    public function destroy(ProgramKerja $program): RedirectResponse
    {
        $program->delete();

        return back()->with('sukses', 'Program kerja dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:rencana,berjalan,selesai,batal'],
            'progres' => ['required', 'integer', 'min:0', 'max:100'],
            'penanggung_jawab' => ['nullable', 'string', 'max:100'],
        ], [
            'nama.required' => 'Nama program kerja wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'progres.max' => 'Progres maksimal 100%.',
        ]);
    }

    private function slugUnik(string $nama): string
    {
        $dasar = Str::slug($nama);
        $slug = $dasar;
        $i = 1;

        while (ProgramKerja::where('slug', $slug)->exists()) {
            $slug = $dasar.'-'.++$i;
        }

        return $slug;
    }
}
