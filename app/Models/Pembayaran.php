<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'metode',
        'status',
        'jumlah_bayar',
        'jumlah_kembali',
        'dibayar_at',
    ];
}