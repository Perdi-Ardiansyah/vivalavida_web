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
     * Memperbarui profil user
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        // Ambil data user yang sedang login
        $user = $request->user();

        // Validasi data yang dikirim dari Flutter
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|string',
            'jenis_kelamin' => 'nullable|string',
            'instagram' => 'nullable|string',
            'kopi_favorit' => 'nullable|string',
        ]);

        // Simpan perubahan ke database
        $user->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ], 200);
    }

    public function uploadPhoto(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = $request->user();

        // Menyimpan file foto ke storage/app/public/profile_photos
        $path = $request->file('foto')->store('profile_photos', 'public');

        // Menyimpan path ke kolom database foto_profil yang baru kita buat
        $user->update(['foto_profil' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diperbarui',
            'path' => $path
        ]);
    }

    /**
     * Memperbarui kata sandi user
     */
    public function changePassword(\Illuminate\Http\Request $request)
    {
        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            // 'confirmed' akan otomatis mengecek kecocokan dengan 'new_password_confirmation'
        ]);

        $user = $request->user();

        // Cek apakah password saat ini benar
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini tidak cocok.'
            ], 400); // 400 Bad Request
        }

        // Simpan password baru (Laravel akan otomatis melakukan hash jika di-setting di casts)
        // Namun, demi keamanan ekstra, kita hash manual di sini
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diubah.'
        ], 200);
    }

    /**
     * Mengambil riwayat sesi login user
     */
    public function getLoginHistory(\Illuminate\Http\Request $request)
    {
        $history = $request->user()->loginHistories()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Menyalakan / Mematikan Autentikasi 2 Faktor (2FA)
     */
    public function toggle2FA(\Illuminate\Http\Request $request)
    {
        $request->validate(['is_enabled' => 'required|boolean']);
        
        $request->user()->update([
            'is_2fa_enabled' => $request->is_enabled
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status 2FA berhasil diperbarui'
        ]);
    }

    /**
     * Keluar dari semua perangkat lain (Revoke Sanctum Tokens)
     */
    public function logoutOtherDevices(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        
        // Hapus semua token login KECUALI token yang sedang dipakai saat ini
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
        
        // Opsional: Hapus juga riwayat login selain yang aktif
        $user->loginHistories()->where('is_active', false)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dari semua perangkat lain'
        ]);
    }

    /**
     * Memperbarui pengaturan notifikasi (disimpan dalam bentuk JSON string)
     */
    public function updateNotificationSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'notification_settings' => 'required|string'
        ]);

        $request->user()->update([
            'notification_settings' => $request->notification_settings
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan notifikasi berhasil diperbarui.'
        ]);
    }
}