<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RewardController extends Controller
{
    /**
     * Menampilkan daftar katalog voucher yang aktif
     */
    public function index(Request $request)
    {
        // Ambil data dari tabel katalog_vouchers yang statusnya aktif dan belum expired
        $vouchers = DB::table('katalog_vouchers')
            ->where('status', 'aktif')
            ->whereDate('berlaku_hingga', '>=', now())
            ->orderBy('poin_dibutuhkan', 'asc')
            ->get()
            ->map(function ($voucher) {
                // MAPPING: Menyesuaikan nama kolom dari DB agar cocok dengan Flutter
                return [
                    'id' => $voucher->id,
                    'nama' => $voucher->judul, // Di DB 'judul', Flutter bacanya 'nama'
                    'deskripsi' => $voucher->deskripsi,
                    'poin_dibutuhkan' => $voucher->poin_dibutuhkan,
                    'stok' => 999, // Karena ini voucher digital, stok diset tidak terbatas
                    'status' => 'active', // Di DB 'aktif', Flutter bacanya 'active'
                    'gambar' => null, // Dikosongkan agar Flutter pakai ikon default
                    'tipe_diskon' => $voucher->tipe_diskon,
                    'nilai_diskon' => $voucher->nilai_diskon,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vouchers
        ], 200);
    }

    /**
     * Memproses penukaran poin dengan voucher
     */
    /**
     * Memproses penukaran poin dengan voucher
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:katalog_vouchers,id',
        ]);

        $user = $request->user();
        $voucher = DB::table('katalog_vouchers')->where('id', $request->reward_id)->first();

        // 1. Validasi Status & Expired Voucher Katalog
        if (!$voucher || $voucher->status !== 'aktif' || Carbon::parse($voucher->berlaku_hingga)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak tersedia atau sudah kedaluwarsa.'
            ], 400);
        }

        // 2. Validasi Poin Pengguna
        if ($user->poin < $voucher->poin_dibutuhkan) {
            return response()->json([
                'success' => false,
                'message' => 'Poin Anda tidak mencukupi untuk menukar voucher ini.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // A. Kurangi poin pengguna
            DB::table('users')->where('id', $user->id)->decrement('poin', $voucher->poin_dibutuhkan);

            // B. Buat Kode Voucher Unik (Contoh: VLV-A8F9B2)
            $kodeUnik = 'VLV-' . strtoupper(substr(md5(uniqid()), 0, 6));

            // C. Masukkan voucher ke dompet pelanggan (Sesuai kolom di phpMyAdmin kamu)
            DB::table('voucher_diskons')->insert([
                'user_id' => $user->id,
                'penukaran_id' => null, // Bisa dikosongkan jika tidak wajib
                'tipe_diskon' => $voucher->tipe_diskon,
                'nilai_diskon' => $voucher->nilai_diskon,
                'kode' => $kodeUnik,
                'status' => 'aktif', // Atau 'belum_dipakai' tergantung kebiasaan databasemu
                'berlaku_hingga' => $voucher->berlaku_hingga,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // D. Catat ke histori poin (Agar pelanggan tahu poinnya dipakai untuk apa)
            // D. Catat ke histori poin
            DB::table('riwayat_poins')->insert([
                'user_id' => $user->id,
                'tipe' => 'keluar', // Mengisi kolom 'tipe'
                'jumlah' => -$voucher->poin_dibutuhkan, // Sesuai dengan nama kolom aslimu: 'jumlah'
                'keterangan' => 'Tukar poin: ' . $voucher->judul,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menukar poin dengan voucher diskon.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan dompet voucher milik pengguna yang sedang login
     */
    public function myVouchers(Request $request)
    {
        $vouchers = DB::table('voucher_diskons')
            ->where('user_id', $request->user()->id)
            ->where('status', 'aktif') // Mengambil yang statusnya masih aktif/belum dipakai
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($v) {
                // Karena di tabel tidak ada judul, kita buat judul dinamis berdasarkan nilai diskon
                $judul = $v->tipe_diskon == 'persen'
                    ? "Diskon Belanja " . $v->nilai_diskon . "%"
                    : "Potongan Harga Rp " . number_format($v->nilai_diskon, 0, ',', '.');

                // Pastikan baris ini di dalam fungsi myVouchers
                return [
                    'id' => $v->id,
                    'kode' => $v->kode,
                    'judul' => $judul,
                    'deskripsi' => 'Berlaku s/d ' . Carbon::parse($v->berlaku_hingga)->format('d M Y'),
                    'tipe_diskon' => $v->tipe_diskon,
                    'nilai_diskon' => (int) $v->nilai_diskon, // Pastikan dikonversi ke Integer
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $vouchers
        ], 200);
    }

   public function getTaxRate()
    {
        // Sekarang ambil dari tabel pengaturans
        $pengaturan = DB::table('pengaturans')->first();
        
        // Jika tabel kosong, set default ke 11
        $pajak = $pengaturan ? $pengaturan->pajak_persen : 11;
        
        return response()->json([
            'tax_rate' => $pajak / 100 // Diubah jadi desimal (0.11) untuk Flutter
        ]);
    }
}