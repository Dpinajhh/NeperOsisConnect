<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\TrendingTopicService;
use Illuminate\View\View;

class FeedController extends Controller
{
    /**
     * Timeline sosial bergaya feed modern: kolom postingan
     * + sidebar Trending Topic (hanya desktop).
     */
    public function index(TrendingTopicService $trending): View
    {
        $user = auth()->user();

        $posts = Post::with([
            'user.kelas.jurusan',
            'komentar' => fn ($q) => $q->whereNull('parent_id')->with(['user', 'balasan.user'])->latest(),
            'topik',
        ])
            ->terlihatUntuk($user)
            ->latest()
            ->paginate(10);

        // Tandai post yang sudah disukai user ini (untuk state tombol)
        $posts->each(fn (Post $post) => $post->sudah_disukai = $post->disukaiOlehUser($user->id));

        $trendingTopik = $trending->daftar(8);

        return view('feed', compact('posts', 'trendingTopik'));
    }
}
