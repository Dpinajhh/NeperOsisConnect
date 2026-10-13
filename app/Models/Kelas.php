<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'tingkat', 'jurusan_id'])]
class Kelas extends Model
{
    protected $table = 'kelas';

    /** @use HasFactory<\Database\Factories\KelasFactory> */
    use HasFactory;

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(User::class, 'kelas_id');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'kelas_id');
    }

    public function getLabelAttribute(): string
    {
        return $this->nama.' ('.$this->jurusan?->kode.')';
    }
}
