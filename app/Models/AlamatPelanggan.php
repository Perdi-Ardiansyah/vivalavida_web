<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlamatPelanggan extends Model
{
    use HasFactory;

    // Arahkan ke tabel yang benar sesuai screenshot
    protected $table = 'alamat_pelanggans';

    // Sesuaikan dengan kolom di database-mu
    protected $fillable = [
        'user_id',
        'label_alamat',
        'alamat_lengkap',
        'catatan_kurir',
        'is_utama'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}