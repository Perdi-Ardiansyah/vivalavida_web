<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk Query Builder

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

    /**
     * Mengambil daftar notifikasi milik pelanggan
     */
    public function myNotifications(Request $request)
    {
        $notifikasis = DB::table('notifikasis')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifikasis
        ], 200);
    }

    /**
     * Menandai semua notifikasi pengguna menjadi sudah dibaca
     */
    public function markAllAsRead(Request $request)
    {
        DB::table('notifikasis')
            ->where('user_id', $request->user()->id)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai dibaca.'
        ], 200);
    }
}