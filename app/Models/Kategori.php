<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'slug', 'tipe', 'warna'])]
class Kategori extends Model
{
    protected $table = 'kategori';

    /** @use HasFactory<\Database\Factories\KategoriFactory> */
    use HasFactory;

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'kategori_id');
    }

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'kategori_id');
    }
}
