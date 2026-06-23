<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use illuminate\Database\Eloquent\Relations\HasMany;

// TAMBAHKAN KOLOM-KOLOM BARU DI DALAM ARRAY FILLABLE INI:
#[Fillable([
    'name', 'email', 'password', 'phone', 'role', 'poin', 'notification_settings',
    'tanggal_lahir', 'jenis_kelamin', 'instagram', 'kopi_favorit',
    'foto_profil', 'is_2fa_enabled', 'two_factor_secret' // <--- Tambahkan 3 ini
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // Baris use ini sudah saya rapikan agar tidak duplikat
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function alamatPelanggans()
    {
        return $this->hasMany(AlamatPelanggan::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke tabel notifikasis
    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }
}