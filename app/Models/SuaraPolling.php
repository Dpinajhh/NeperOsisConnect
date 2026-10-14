<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['polling_id', 'pilihan_id', 'user_id'])]
class SuaraPolling extends Model
{
    protected $table = 'polling_suara';

    /** @use HasFactory<\Database\Factories\SuaraPollingFactory> */
    use HasFactory;

    public function polling(): BelongsTo
    {
        return $this->belongsTo(Polling::class, 'polling_id');
    }

    public function pilihan(): BelongsTo
    {
        return $this->belongsTo(PilihanPolling::class, 'pilihan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
