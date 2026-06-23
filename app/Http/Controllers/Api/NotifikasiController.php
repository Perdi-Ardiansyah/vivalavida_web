<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ditambahkan

class NotifikasiController extends Controller
{
    // Ambil semua notifikasi user
    public function index(Request $request)
    {
        $notif = DB::table('notifikasis')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true, 
            'data' => $notif
        ]);
    }

    // Tandai semua sebagai sudah dibaca
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
            'message' => 'Semua ditandai sudah dibaca'
        ]);
    }
}