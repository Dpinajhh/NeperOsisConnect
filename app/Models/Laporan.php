<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'pelapor_id',
    'laporanable_type',
    'laporanable_id',
    'alasan',
    'status',
    'catatan_admin',
    'ditangani_oleh',
])]
class Laporan extends Model
{
    protected $table = 'laporan';

    /** @use HasFactory<\Database\Factories\LaporanFactory> */
    use HasFactory;

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function ditanganiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function laporanable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /** Label yang enak dibaca untuk objek yang dilaporkan */
    public function getObjekLabelAttribute(): string
    {
        return match (class_basename($this->laporanable_type)) {
            'Post' => 'Postingan',
            'Komentar' => 'Komentar',
            'Pengumuman' => 'Pengumuman',
            'Berita' => 'Berita',
            default => 'Konten',
        };
    }
}
