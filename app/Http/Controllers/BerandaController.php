<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\View\View;

class BerandaController extends Controller
{
    /**
     * Landing page publik: memperkenalkan Osis Connect
     * sebagai platform siswa SMK Negeri 1 Cirebon.
     */
    public function index(): View
    {
        $pengumumanTerbaru = Pengumuman::terbit()
            ->latest('published_at')
            ->limit(3)
            ->get(['id', 'judul', 'slug', 'published_at']);

        return view('beranda', [
            'statistik' => [
                'siswa' => User::where('role', 'siswa')->count(),
                'pengumuman' => Pengumuman::terbit()->count(),
                'berita' => Berita::terbit()->count(),
            ],
            'pengumumanTerbaru' => $pengumumanTerbaru,
        ]);
    }
}
