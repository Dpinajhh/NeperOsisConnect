<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Kolom yang boleh diisi massal.
 *
 * SENGAJA TIDAK DIDAFTARKAN di sini: role, is_active, is_banned, last_login_at.
 * Keempatnya hanya boleh diubah server lewat forceFill() — supaya tidak ada
 * jalur di mana input pengguna bisa menaikkan peran atau membuka blokir akunnya
 * sendiri (mis. bila suatu saat muncul $user->update($request->all())).
 */
#[Fillable([
    'name',
    'email',
    'password',
    'nis',
    'kelas_id',
    'avatar',
    'bio',
    'telepon',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // ─── Relasi ───────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(Komentar::class, 'user_id');
    }

    /** Post yang disukai user ini */
    public function postDisukai(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_suka', 'user_id', 'post_id')
            ->withTimestamps();
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'user_id');
    }

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'user_id');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    /** Pengumuman yang sudah dibaca user ini */
    public function pembaca(): BelongsToMany
    {
        return $this->belongsToMany(Pengumuman::class, 'pengumuman_baca', 'user_id', 'pengumuman_id')
            ->withPivot('dibaca_at')
            ->withTimestamps();
    }

    public function laporanDibuat(): HasMany
    {
        return $this->hasMany(Laporan::class, 'pelapor_id');
    }

    // ─── Helper role ──────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /** Admin & guru boleh mengelola konten (pengumuman, berita, moderasi) */
    public function isPengelola(): bool
    {
        return in_array($this->role, ['admin', 'guru'], true);
    }

    /** Jurusan siswa diambil dari kelasnya */
    public function getJurusanIdAttribute(): ?int
    {
        return $this->kelas?->jurusan_id;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=4f46e5&color=fff';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_banned' => 'boolean',
        ];
    }
}
