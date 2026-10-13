<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'post_id', 'parent_id', 'konten'])]
class Komentar extends Model
{
    protected $table = 'komentar';

    /** @use HasFactory<\Database\Factories\KomentarFactory> */
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Komentar::class, 'parent_id');
    }

    public function balasan(): HasMany
    {
        return $this->hasMany(Komentar::class, 'parent_id');
    }

    public function laporan(): MorphMany
    {
        return $this->morphMany(Laporan::class, 'laporanable');
    }

    /** Cek apakah komentar ini adalah balasan (bukan komentar utama) */
    public function isBalasan(): bool
    {
        return $this->parent_id !== null;
    }

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }
}
