<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

    /** Topik (hashtag) yang terkait post ini */
    public function topik(): BelongsToMany
    {
        return $this->belongsToMany(Topik::class, 'post_topik', 'post_id', 'topik_id')
            ->withTimestamps();
    }

    public function laporan(): MorphMany
    {
        return $this->morphMany(Laporan::class, 'laporanable');
    }

    /**
     * Post yang boleh dilihat user sesuai visibilitas:
     * 'sekolah' terlihat semua; 'kelas' hanya sekelas; 'jurusan' hanya sejurusan.
     */
    public function scopeTerlihatUntuk(Builder $query, ?User $user): void
    {
        if (! $user) {
            return;
        }

        $query->where(function (Builder $q) use ($user) {
            $q->where('visibilitas', 'sekolah');

            if ($user->kelas_id) {
                $q->orWhere(fn (Builder $q2) => $q2->where('visibilitas', 'kelas')
                    ->whereHas('user', fn (Builder $u) => $u->where('kelas_id', $user->kelas_id)));

                if ($jurusanId = $user->kelas?->jurusan_id) {
                    $q->orWhere(fn (Builder $q2) => $q2->where('visibilitas', 'jurusan')
                        ->whereHas('user.kelas', fn (Builder $k) => $k->where('jurusan_id', $jurusanId)));
                }
            }
        });
    }

    /** Ambil semua hashtag (#contoh) dari isi post */
    public static function ekstrakTopik(string $konten): array
    {
        preg_match_all('/#([\p{L}\p{N}_]+)/u', $konten, $cocok);

        return collect($cocok[1])
            ->map(fn ($t) => Str::lower($t))
            ->unique()
            ->take(5)
            ->values()
            ->all();
    }

    /** Konten dengan hashtag jadi link menuju halaman topik (aman dari XSS) */
    public function getKontenHtmlAttribute(): string
    {
        return preg_replace_callback('/#([\p{L}\p{N}_]+)/u', function (array $cocok) {
            $nama = e($cocok[1]);

            return '<a href="'.route('topik.show', Str::slug($cocok[1])).'" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">#'.$nama.'</a>';
        }, e($this->konten));
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
