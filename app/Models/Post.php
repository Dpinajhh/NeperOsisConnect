<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'konten', 'gambar', 'visibilitas', 'jumlah_suka', 'jumlah_komentar'])]
class Post extends Model
{
    protected $table = 'posts';

    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(Komentar::class, 'post_id');
    }

    /** Hanya komentar utama (bukan balasan) */
    public function komentarUtama(): HasMany
    {
        return $this->hasMany(Komentar::class, 'post_id')->whereNull('parent_id');
    }

    /** User yang menyukai post ini */
    public function disukaiOleh(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_suka', 'post_id', 'user_id')
            ->withTimestamps();
    }

    public function laporan(): MorphMany
    {
        return $this->morphMany(Laporan::class, 'laporanable');
    }

    /** Cek apakah user tertentu sudah menyukai post ini */
    public function disukaiOlehUser(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return $this->disukaiOleh()->where('users.id', $userId)->exists();
    }

    protected function casts(): array
    {
        return [
            'jumlah_suka' => 'integer',
            'jumlah_komentar' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }
}
