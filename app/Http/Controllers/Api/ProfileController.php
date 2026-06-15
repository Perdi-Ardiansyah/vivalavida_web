<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiwayatLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update Notification Settings (Mengubah status toggle JSON di DB)
     */
    public function updateNotificationSettings(Request $request)
    {
        $user = $request->user();

        // Validasi status sakelar toggle dari Flutter
        $request->validate([
            'order_updates' => 'required|boolean',
            'vouchers' => 'required|boolean',
            'news' => 'required|boolean',
            'login_alerts' => 'required|boolean',
            'account_activity' => 'required|boolean',
        ]);

        // Simpan data ke kolom JSON user
        $user->notification_settings = $request->only([
            'order_updates', 'vouchers', 'news', 'login_alerts', 'account_activity'
        ]);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan notifikasi berhasil disimpan',
            'data' => $user->notification_settings
        ], 200);
    }

    /**
     * Mengubah Kata Sandi dari menu Account Security
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah password lama sesuai
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama tidak sesuai'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diubah'
        ], 200);
    }

    /**
     * Mengambil daftar Riwayat Sesi Login perangkat
     */
    public function getLoginHistory(Request $request)
    {
        // Mengambil riwayat perangkat yang terdaftar di tabel riwayat_logins
        $history = RiwayatLogin::where('user_id', $request->user()->id)
            ->orderBy('last_active_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ], 200);
    }
}