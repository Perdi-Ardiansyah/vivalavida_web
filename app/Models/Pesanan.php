<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan mass assignment
    protected $fillable = [
        'user_id',
        'meja_id',
        'alamat_pengiriman_id',
        'tipe_pesanan',
        'sumber_pesanan',
        'status',
        'total_harga',
        'voucher_id',
        'diskon_voucher',
        'total_akhir',
        'catatan',
    ];
    /**
     * Relasi ke PesananItem (Satu pesanan punya banyak item)
     */
    public function items()
    {
        return $this->hasMany(PesananItem::class, 'pesanan_id');
    }
    /**
     * Relasi ke User (Satu pesanan dimiliki oleh satu user)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Relasi ke Pembayaran (Satu pesanan punya satu/banyak riwayat pembayaran)
     * (Saya tambahkan ini juga untuk jaga-jaga karena strukmu butuh data metode pembayaran)
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id');
    }

    // ... relasi kamu yang sudah ada biarkan saja di bawah sini
}