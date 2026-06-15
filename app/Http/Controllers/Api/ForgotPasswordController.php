<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /**
     * 1. Kirim Instruksi Lupa Password (Membangkitkan OTP)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat email tidak terdaftar'
            ], 442);
        }

        // Di sini kamu bisa mengintegrasikan sistem pengiriman email/OTP asli.
        // Untuk kebutuhan development, kita buat simulasi kode OTP sukses.
        return response()->json([
            'success' => true,
            'message' => 'Instruksi pemulihan dan kode OTP telah dikirim ke email Anda',
            'email' => $request->email
        ], 200);
    }

    /**
     * 2. Verifikasi Kode OTP (6 Digit)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Simulasi validasi OTP (misal kode sukses selalu '123456')
        if ($request->otp !== '123456') {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah atau kadaluarsa'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil diverifikasi'
        ], 200);
    }

    /**
     * 3. Simpan Kata Sandi Baru (Reset)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed', // mencakup validasi syarat minimal 8 karakter di UI
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal, pastikan kata sandi minimal 8 karakter',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi Anda berhasil diperbarui, silakan login kembali'
        ], 200);
    }
}