<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Mengambil daftar riwayat pesanan (Untuk order_list_screen.dart)
     */
    public function index(Request $request)
    {
        // Ambil semua pesanan milik user yang sedang aktif
        $orders = Pesanan::with(['items.menu'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }
}