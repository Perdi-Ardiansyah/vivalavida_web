<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Gunakan DB facade jika belum buat Model Promo

class PromoController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel promos (terbaru di atas)
        $promos = DB::table('promos')->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $promos
        ]);
    }
}