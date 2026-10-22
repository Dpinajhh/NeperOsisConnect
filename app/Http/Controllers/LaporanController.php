<?php

namespace App\Http\Controllers;

use App\Models\Komentar;
use App\Models\Laporan;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Peta tipe konten yang bisa dilaporkan → modelnya.
     */
    private const TIPE = [
        'post' => Post::class,
        'komentar' => Komentar::class,
    ];

    /**
     * Catat laporan konten yang tidak pantas.
     * Satu user hanya boleh melaporkan objek yang sama sekali.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => ['required', 'in:post,komentar'],
            'id' => ['required', 'integer'],
            'alasan' => ['required', 'string', 'max:500'],
        ], [
            'tipe.required' => 'Jenis konten tidak dikenali.',
            'tipe.in' => 'Jenis konten tidak dikenali.',
            'id.required' => 'Konten tidak ditemukan.',
            'alasan.required' => 'Tulis alasan pelaporan.',
            'alasan.max' => 'Alasan maksimal 500 karakter.',
        ]);

        $model = self::TIPE[$validated['tipe']];
        $objek = $model::find($validated['id']);

        if (! $objek) {
            return back()->with('error', 'Konten sudah dihapus atau tidak ditemukan.');
        }

        $user = $request->user();

        // Jangan izinkan melaporkan konten yang tidak boleh dilihat.
        // Tanpa ini, ID post/komentar dari kelas atau jurusan lain bisa
        // dilaporkan hanya dengan menebak angkanya.
        $postTerkait = $objek instanceof Post ? $objek : $objek->post;
        $terlihat = $postTerkait
            && Post::terlihatUntuk($user)->whereKey($postTerkait->getKey())->exists();

        if (! $terlihat) {
            return back()->with('error', 'Konten sudah dihapus atau tidak ditemukan.');
        }

        // Tidak boleh melaporkan konten sendiri
        if ($objek->user_id === $user->id) {
            return back()->with('error', 'Kamu tidak bisa melaporkan konten milikmu sendiri.');
        }

        // Cek duplikat
        $ada = Laporan::where('pelapor_id', $user->id)
            ->where('laporanable_type', $model)
            ->where('laporanable_id', $objek->id)
            ->where('status', 'open')
            ->exists();

        if ($ada) {
            return back()->with('error', 'Konten ini sudah pernah kamu laporkan dan sedang ditinjau pengurus.');
        }

        Laporan::create([
            'pelapor_id' => $user->id,
            'laporanable_type' => $model,
            'laporanable_id' => $objek->id,
            'alasan' => $validated['alasan'],
            'status' => 'open',
        ]);

        return back()->with('sukses', 'Laporan terkirim. Pengurus OSIS akan meninjau secepatnya. Terima kasih sudah menjaga kenyamanan bersama! 🙏');
    }
}
