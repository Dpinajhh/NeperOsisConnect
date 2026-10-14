<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'jabatan', 'bidang', 'foto', 'urutan'])]
class Kepengurusan extends Model
{
    protected $table = 'kepengurusan';

    /** @use HasFactory<\Database\Factories\KepengurusanFactory> */
    use HasFactory;

    /** Daftar pengurus diurutkan. */
    public function scopeUrut(Builder $query): void
    {
        $query->orderBy('urutan')->orderBy('nama');
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/'.$this->foto)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->nama).'&background=4f46e5&color=fff';
    }
}
