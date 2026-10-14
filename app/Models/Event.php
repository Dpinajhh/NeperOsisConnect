<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama', 'slug', 'deskripsi', 'poster', 'lokasi', 'waktu_mulai', 'waktu_selesai',
    'kuota', 'status', 'user_id', 'program_kerja_id',
])]
class Event extends Model
{
    protected $table = 'event';

    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Route model binding memakai slug. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    public function peserta(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_peserta', 'event_id', 'user_id')->withTimestamps();
    }

    /** Hanya event terbit. */
    public function scopeTerbit(Builder $query): void
    {
        $query->where('status', 'terbit');
    }

    /** Event yang belum/waktu berlangsung. */
    public function scopeMendatang(Builder $query): void
    {
        $query->where('waktu_mulai', '>=', now()->subDay())
            ->orderBy('waktu_mulai');
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster ? asset('storage/'.$this->poster) : null;
    }

    public function penuh(): bool
    {
        return $this->kuota !== null && $this->peserta()->count() >= $this->kuota;
    }

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'kuota' => 'integer',
        ];
    }
}
