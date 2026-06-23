<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * 1. Kirim Instruksi Lupa Password (Membangkitkan OTP sungguhan)
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat email tidak terdaftar di sistem kami'
            ], 404);
        }

        // Menghasilkan 6 digit angka acak (contoh: 048291)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Menyimpan OTP ke tabel bawaan Laravel
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // Mengirimkan OTP via Email
        try {
            Mail::raw("Halo {$user->name},\n\nKode OTP untuk mengatur ulang kata sandi Anda adalah: {$otp}\n\nKode ini berlaku selama 15 menit. Tolong jangan berikan kode ini kepada siapa pun.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode OTP Reset Password - Vivalavida');
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email. Pastikan pengaturan SMTP di file .env sudah benar.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6-digit telah dikirim ke email Anda',
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

        $record = DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->where('token', $request->otp)
                    ->first();

        // Cek apakah OTP salah atau tidak ditemukan
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah.'
            ], 400);
        }

        // Cek apakah OTP sudah kadaluarsa (lebih dari 15 menit)
        $createdAt = Carbon::parse($record->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 15) {
            // Hapus OTP yang kadaluarsa
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.'
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
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal, pastikan kata sandi minimal 8 karakter dan cocok',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan user masih punya token yang valid sebelum mereset
        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi reset kata sandi tidak valid. Silakan ulangi dari awal.'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        // Update kata sandi baru
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token OTP agar tidak bisa dipakai lagi
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui. Silakan login kembali.'
        ], 200);
    }
}