<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AturanPoin;
use App\Models\VoucherDiskon;
use App\Models\PenukaranPoin;
use App\Models\RiwayatPoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RewardController extends Controller
{
    /**
     * 1. Mengambil info poin dan daftar voucher milik user (Untuk rewards_screen.dart)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Ambil voucher milik user yang statusnya masih aktif
        $vouchers = VoucherDiskon::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->where('berlaku_hingga', '>=', now())
            ->get();

        // Ambil pilihan penukaran poin yang tersedia di kafe
        $availableRewards = AturanPoin::where('aktif', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_poin' => $user->poin,
                'my_vouchers' => $vouchers,
                'available_rewards' => $availableRewards
            ]
        ], 200);
    }

    /**
     * 2. Menukarkan Poin dengan Voucher Baru
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'aturan_poin_id' => 'required|exists:aturan_poins,id'
        ]);

        $user = $request->user();
        $aturan = AturanPoin::find($request->aturan_poin_id);

        // Cek kecukupan poin
        if ($user->poin < $aturan->point_per_voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Poin Anda tidak mencukupi untuk melakukan penukaran ini'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Potong Poin User
            $user->poin -= $aturan->point_per_voucher;
            $user->save();

            // 2. Catat Mutasi Keluar di Riwayat Poin
            RiwayatPoin::create([
                'user_id' => $user->id,
                'tipe' => 'keluar',
                'jumlah' => $aturan->point_per_voucher,
                'keterangan' => 'Penukaran voucher: ' . $aturan->nama,
            ]);

            // 3. Catat transaksi penukaran
            $penukaran = PenukaranPoin::create([
                'user_id' => $user->id,
                'aturan_poin_id' => $aturan->id,
                'poin_ditukar' => $aturan->point_per_voucher,
                'jumlah_voucher' => 1,
                'status' => 'disetujui'
            ]);

            // 4. Terbitkan Kode Voucher Diskon Baru untuk User
            $voucher = VoucherDiskon::create([
                'user_id' => $user->id,
                'penukaran_id' => $penukaran->id,
                'tipe_diskon' => $aturan->tipe_diskon,
                'nilai_diskon' => $aturan->nilai_diskon,
                'kode' => 'VLV-' . strtoupper(Str::random(8)),
                'status' => 'aktif',
                'berlaku_hingga' => now()->addDays(30), // Valid 30 hari kedepan
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Poin berhasil ditukarkan dengan Voucher!',
                'data' => $voucher
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Proses klaim penukaran gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}