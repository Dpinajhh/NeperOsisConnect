<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'periode', 'visi', 'misi', 'sejarah', 'sambutuan', 'sambutuan_pengirim',
    'alamat', 'email', 'telepon', 'instagram',
])]
class ProfilOsis extends Model
{
    protected $table = 'profil_osis';

    /** @use HasFactory<\Database\Factories\ProfilOsisFactory> */
    use HasFactory;

    /**
     * Ambil baris profil (selalu satu); buat bila belum ada.
     */
    public static function ambil(): self
    {
        return static::query()->firstOrCreate([]);
    }

    protected function casts(): array
    {
        return [];
    }
}
