<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama', 'slug', 'deskripsi', 'bidang', 'tanggal_mulai', 'tanggal_selesai',
    'status', 'progres', 'penanggung_jawab', 'user_id',
])]
class ProgramKerja extends Model
{
    protected $table = 'program_kerja';

    /** @use HasFactory<\Database\Factories\ProgramKerjaFactory> */
    use HasFactory;

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Event::class, 'program_kerja_id');
    }

    /** Route model binding memakai slug. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Daftar terbaru; yang diutamakan: rencana & berjalan. */
    public function scopeTerbaru(Builder $query): void
    {
        $query->orderByRaw("CASE status WHEN 'berjalan' THEN 0 WHEN 'rencana' THEN 1 WHEN 'selesai' THEN 2 ELSE 3 END")
            ->orderByDesc('tanggal_mulai')
            ->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'rencana' => 'Rencana',
            'berjalan' => 'Berjalan',
            'selesai' => 'Selesai',
            'batal' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusWarnaAttribute(): string
    {
        return match ($this->status) {
            'rencana' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            'berjalan' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200',
            'selesai' => 'bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-200',
            'batal' => 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-200',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'progres' => 'integer',
        ];
    }
}
