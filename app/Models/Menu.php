<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi secara massal (mass assignment)
    protected $guarded = []; 

    /**
     * Relasi: Satu Menu dimiliki oleh satu Kategori
     */
    public function kategori()
    {
        // 'kategori_id' adalah nama kolom foreign key di tabel menus
        return $this->belongsTo(KategoriMenu::class, 'kategori_id');
    }
}