<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'kategori_id',
    'judul',
    'slug',
    'ringkasan',
    'konten',
    'cover',
    'status',
    'is_headline',
    'published_at',
    'dilihat',
])]
class Berita extends Model
{
    protected $table = 'berita';

    /** @use HasFactory<\Database\Factories\BeritaFactory> */
    use HasFactory, SoftDeletes;

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /** Route model binding memakai slug. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeTerbit(Builder $query): void
    {
        $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeHeadline(Builder $query): void
    {
        $query->where('is_headline', true);
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/'.$this->cover) : null;
    }

    protected function casts(): array
    {
        return [
            'is_headline' => 'boolean',
            'published_at' => 'datetime',
            'dilihat' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }
}
