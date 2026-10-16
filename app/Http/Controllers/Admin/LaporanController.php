<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct()
    {
        // Semua aksi di kelas ini khusus admin & guru (dicek di route).
    }

    /**
     * Daftar laporan masuk, diurutkan lama dulu & yang terbuka di atas.
     */
    public function index(Request $request)
    {
        $filter = $request->string('status')->value() ?: 'semua';

        $laporan = Laporan::query()
            ->with(['pelapor', 'ditanganiOleh', 'laporanable'])
            ->when($filter !== 'semua', fn ($q) => $q->where('status', $filter))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $ringkas = [
            'semua' => Laporan::count(),
            'open' => Laporan::where('status', 'open')->count(),
            'diproses' => Laporan::where('status', 'diproses')->count(),
            'selesai' => Laporan::where('status', 'selesai')->count(),
            'ditolak' => Laporan::where('status', 'ditolak')->count(),
        ];

        return view('admin.laporan', compact('laporan', 'ringkas', 'filter'));
    }

    /**
     * Ubah status laporan: diproses / selesai / ditolak.
     */
    public function perbarui(Request $request, Laporan $laporan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diproses,selesai,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ], [
            'status.in' => 'Status tidak valid.',
        ]);

        $laporan->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
            'ditangani_oleh' => $request->user()->id,
        ]);

        return back()->with('sukses', 'Status laporan diperbarui menjadi "'.ucfirst($validated['status']).'".');
    }

    /**
     * Tindak lanjut: konten yang dilaporkan dihapus.
     * Status laporan otomatis jadi "selesai".
     */
    public function hapusKonten(Request $request, Laporan $laporan): RedirectResponse
    {
        $objek = $laporan->laporanable;

        if ($objek) {
            if (method_exists($objek, 'delete')) {
                // Komentar: sinkronkan counter pada post induknya
                if ($objek instanceof \App\Models\Komentar) {
                    $jumlah = $objek->balasan()->count() + 1;
                    $objek->post?->decrement('jumlah_komentar', $jumlah);
                }
                $objek->delete();
            }
        }

        $laporan->update([
            'status' => 'selesai',
            'catatan_admin' => trim(($laporan->catatan_admin ? $laporan->catatan_admin."\n" : '').'Konten dihapus pada '.now()->format('d M Y H:i')),
            'ditangani_oleh' => $request->user()->id,
        ]);

        return back()->with('sukses', 'Konten yang dilaporkan sudah dihapus dan laporan ditandai selesai.');
    }
}
