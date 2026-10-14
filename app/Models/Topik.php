<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nama', 'slug', 'jumlah_post'])]
class Topik extends Model
{
    protected $table = 'topik';

    /** @use HasFactory<\Database\Factories\TopikFactory> */
    use HasFactory;

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_topik', 'topik_id', 'post_id')->withTimestamps();
    }

    /**
     * Trending Topic — algoritma:
     * setiap post bernilai 1 aktivitas, ditambah setiap komentar
     * dan setiap suka pada post bertopik tersebut (7 hari terakhir).
     * Topik dengan skor tertinggi menjadi trending.
     */
    public function scopeTrending(Builder $query, int $limit = 8): void
    {
        $query->join('post_topik', 'topik.id', '=', 'post_topik.topik_id')
            ->join('posts', 'posts.id', '=', 'post_topik.post_id')
            ->where('posts.created_at', '>=', now()->subDays(7))
            ->groupBy('topik.id')
            ->selectRaw('topik.*, (COUNT(DISTINCT posts.id) + SUM(posts.jumlah_komentar) + SUM(posts.jumlah_suka)) as skor')
            ->orderByDesc('skor')
            ->limit($limit);
    }

    protected function casts(): array
    {
        return [
            'jumlah_post' => 'integer',
        ];
    }
}
