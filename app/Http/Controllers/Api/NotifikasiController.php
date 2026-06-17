<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // Ambil semua notifikasi user
    public function index(Request $request)
    {
        $notif = $request->user()->notifikasis()->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $notif]);
    }

    // Tandai semua sebagai sudah dibaca
    public function markAllAsRead(Request $request)
    {
        $request->user()->notifikasis()->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true, 'message' => 'Semua ditandai sudah dibaca']);
    }
}