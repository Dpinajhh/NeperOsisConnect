<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Laporan;
use App\Models\Pengumuman;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman utama setelah login: sapaan, ringkasan info,
     * pengumuman & berita terbaru, serta statistik untuk pengelola.
     */
    public function index(): View
    {
        $user = Auth::user();

        $pengumumanTerbaru = Pengumuman::terbit()
            ->untuk($user)
            ->with('penulis', 'kategori')
            ->orderByDesc('is_pinned')
            ->latest('published_at')
            ->limit(4)
            ->get();

        $beritaTerbaru = Berita::terbit()
            ->with('kategori')
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Statistik khusus admin & guru (pengelola konten)
        $statistik = [];
        if ($user->isPengelola()) {
            $statistik = [
                'siswa' => User::where('role', 'siswa')->count(),
                'post' => Post::count(),
                'pengumuman' => Pengumuman::count(),
                'berita' => Berita::count(),
                'laporan' => Laporan::where('status', 'open')->count(),
            ];
        }

        return view('dashboard', compact('pengumumanTerbaru', 'beritaTerbaru', 'statistik'));
    }
}
