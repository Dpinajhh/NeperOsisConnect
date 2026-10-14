<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['polling_id', 'teks', 'urutan'])]
class PilihanPolling extends Model
{
    protected $table = 'polling_pilihan';

    /** @use HasFactory<\Database\Factories\PilihanPollingFactory> */
    use HasFactory;

    public function polling(): BelongsTo
    {
        return $this->belongsTo(Polling::class, 'polling_id');
    }

    public function suara(): HasMany
    {
        return $this->hasMany(SuaraPolling::class, 'pilihan_id');
    }
}
