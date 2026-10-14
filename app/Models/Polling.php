<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['pertanyaan', 'slug', 'deskripsi', 'status', 'user_id'])]
class Polling extends Model
{
    protected $table = 'polling';

    /** @use HasFactory<\Database\Factories\PollingFactory> */
    use HasFactory;

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pilihan(): HasMany
    {
        return $this->hasMany(PilihanPolling::class, 'polling_id')->orderBy('urutan');
    }

    public function suara(): HasMany
    {
        return $this->hasMany(SuaraPolling::class, 'polling_id');
    }

    /** Polling yang sedang berlangsung. */
    public function scopeAktif(Builder $query): void
    {
        $query->where('status', 'terbit');
    }

    /** Route model binding memakai slug. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function totalSuara(): int
    {
        return $this->suara()->count();
    }

    /** Sudah pengguna ini memilih? */
    public function sudahMemilih(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return $this->suara()->where('user_id', $userId)->exists();
    }

    /** Hasil: persentase + jumlah suara per pilihan. */
    public function hasil(): array
    {
        $total = $this->totalSuara() ?: 1;

        return $this->pilihan()->get()->map(fn (PilihanPolling $p) => [
            'id' => $p->id,
            'teks' => $p->teks,
            'jumlah' => $p->suara()->count(),
            'persen' => round($p->suara()->count() / $total * 100),
        ])->all();
    }

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }
}
