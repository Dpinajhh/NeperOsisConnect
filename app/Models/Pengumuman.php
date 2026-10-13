<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'user_id',
    'kategori_id',
    'judul',
    'slug',
    'konten',
    'cover',
    'target',
    'kelas_id',
    'jurusan_id',
    'is_pinned',
    'published_at',
    'dilihat',
])]
class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    /** @use HasFactory<\Database\Factories\PengumumanFactory> */
    use HasFactory;

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function laporan(): MorphMany
    {
        return $this->morphMany(Laporan::class, 'laporanable');
    }

    /** User yang sudah membaca pengumuman ini */
    public function pembaca(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pengumuman_baca', 'pengumuman_id', 'user_id')
            ->withPivot('dibaca_at')
            ->withTimestamps();
    }

    /** Hanya pengumuman yang sudah terbit */
    public function scopeTerbit(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Pengumuman yang relevan untuk seorang siswa.
     * 'semua' selalu tampil; 'kelas'/'jurusan' harus cocok.
     */
    public function scopeUntuk(?User $user, Builder $query): void
    {
        $query->where(function (Builder $q) use ($user) {
            // Selalu tampil: pengumuman untuk seluruh sekolah
            $q->where('target', 'semua');

            if (! $user?->kelas_id) {
                return;
            }

            // Pengumuman khusus kelas siswa tersebut
            $q->orWhere(function (Builder $q2) use ($user) {
                $q2->where('target', 'kelas')->where('kelas_id', $user->kelas_id);
            });

            // Pengumuman khusus jurusan siswa tersebut
            $jurusanId = $user->kelas?->jurusan_id;
            if ($jurusanId) {
                $q->orWhere(function (Builder $q3) use ($jurusanId) {
                    $q3->where('target', 'jurusan')->where('jurusan_id', $jurusanId);
                });
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
            'dilihat' => 'integer',
        ];
    }
}
