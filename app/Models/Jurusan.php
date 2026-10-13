<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['kode', 'nama', 'keterangan'])]
class Jurusan extends Model
{
    protected $table = 'jurusan';

    /** @use HasFactory<\Database\Factories\JurusanFactory> */
    use HasFactory;

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'jurusan_id');
    }

    /** Semua siswa yang ada di jurusan ini (lewat kelas) */
    public function siswa(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Kelas::class);
    }
}
