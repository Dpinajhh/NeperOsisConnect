<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Notifikasi pengguna: daftar, tandai dibaca, dan antar ke tujuannya.
 */
class NotifikasiController extends Controller
{
    /** Daftar notifikasi milik pengguna yang sedang masuk. */
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->value() === 'belum' ? 'belum' : 'semua';

        $notifikasi = $request->user()->notifikasi()
            ->when($filter === 'belum', fn ($q) => $q->belumDibaca())
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('notifikasi', [
            'notifikasi' => $notifikasi,
            'filter' => $filter,
            'jumlahBelumDibaca' => $request->user()->notifikasi()->belumDibaca()->count(),
        ]);
    }

    /** Tandai satu notifikasi sudah dibaca, lalu antar ke tujuannya. */
    public function baca(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === $request->user()->id, 403);

        $notifikasi->tandaiDibaca();

        $tujuan = $this->tujuanInternal($notifikasi->url);

        return $tujuan ? redirect($tujuan) : back();
    }

    /** Tandai semua notifikasi sudah dibaca. */
    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->notifikasi()->belumDibaca()->update(['dibaca_at' => now()]);

        return back()->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Hanya izinkan tujuan yang masih di dalam aplikasi sendiri
     * (mencegah open redirect bila suatu saat kolom url terisi dari luar).
     */
    private function tujuanInternal(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $basis = rtrim(url('/'), '/');

        return str_starts_with($url, $basis.'/') || str_starts_with($url, '/')
            ? $url
            : null;
    }
}
