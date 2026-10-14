<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topik;
use App\Services\TrendingTopicService;
use Illuminate\View\View;

class TopikController extends Controller
{
    /** Daftar post yang memuat topik (hashtag) tertentu. */
    public function show(Topik $topik, TrendingTopicService $trending): View
    {
        $posts = Post::with([
            'user.kelas.jurusan',
            'komentar' => fn ($q) => $q->whereNull('parent_id')->with(['user', 'balasan.user'])->latest(),
            'topik',
        ])
            ->terlihatUntuk(auth()->user())
            ->whereHas('topik', fn ($q) => $q->where('topik.id', $topik->id))
            ->latest()
            ->paginate(10);

        $posts->each(fn (Post $post) => $post->sudah_disukai = $post->disukaiOlehUser(auth()->id()));

        // Topik sedang trending?
        $peringkat = $trending->daftar(20)->search(fn ($t) => $t->id === $topik->id);
        $peringkat = $peringkat === false ? null : $peringkat + 1;

        return view('topik', compact('posts', 'topik', 'peringkat'));
    }
}
