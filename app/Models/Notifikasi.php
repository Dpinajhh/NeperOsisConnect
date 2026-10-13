<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'tipe', 'judul', 'pesan', 'url', 'dibaca_at'])]
class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    /** @use HasFactory<\Database\Factories\NotifikasiFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeBelumDibaca(Builder $query): void
    {
        $query->whereNull('dibaca_at');
    }

    public function tandaiDibaca(): bool
    {
        return $this->forceFill(['dibaca_at' => now()])->save();
    }
}
