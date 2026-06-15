<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'menu_id',
        'jumlah',
        'harga_satuan',
        'opsi_tambahan',
    ];

    /**
     * Relasi ke Menu (Satu item pesanan merujuk ke satu menu)
     */
    public function menu()
    {
        return $this->belongsTo(\App\Models\Menu::class, 'menu_id');
    }
}