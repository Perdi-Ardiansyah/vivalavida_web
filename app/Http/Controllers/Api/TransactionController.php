<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * 1. Mengirim Pesanan Baru (Dari checkout_screen.dart)
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'tipe_pesanan' => 'required|in:dine_in,takeaway,delivery',
            'meja_id' => 'nullable|exists:mejas,id',
            'alamat_pengiriman_id' => 'nullable|exists:alamat_pelanggans,id',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric',
            'items.*.opsi_tambahan' => 'nullable|array',
            'voucher_id' => 'nullable|exists:voucher_diskons,id',
            'diskon_voucher' => 'nullable|numeric',
            'metode_pembayaran' => 'required|in:cash,qris,gopay',
        ]);

        try {
            DB::beginTransaction();

            $totalHarga = 0;
            foreach ($request->items as $item) {
                $totalHarga += $item['harga_satuan'] * $item['jumlah'];
            }

            $diskon = $request->diskon_voucher ?? 0;
            $totalAkhir = $totalHarga - $diskon;

            $pesanan = Pesanan::create([
                'user_id' => $request->user()->id,
                'meja_id' => $request->meja_id,
                'alamat_pengiriman_id' => $request->alamat_pengiriman_id,
                'tipe_pesanan' => $request->tipe_pesanan,
                'sumber_pesanan' => 'app',
                'status' => 'new',
                'total_harga' => $totalHarga,
                'voucher_id' => $request->voucher_id,
                'diskon_voucher' => $diskon,
                'total_akhir' => $totalAkhir,
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                PesananItem::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'opsi_tambahan' => isset($item['opsi_tambahan']) ? json_encode($item['opsi_tambahan']) : null,
                ]);
            }

            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode' => $request->metode_pembayaran,
                'status' => 'unpaid', 
                'jumlah_bayar' => $totalAkhir,
                'jumlah_kembali' => 0,
                'dibayar_at' => null,
            ]);

            // --- INTEGRASI XENDIT INVOICE (WEB LINK) ---
            $qrUrl = null;
            $orderId = 'VIVA-' . $pesanan->id . '-' . time();

            if (in_array($request->metode_pembayaran, ['qris', 'gopay'])) {
                $secretKey = env('XENDIT_SECRET_KEY');
                
                // Jika Secret Key kosong, gagalkan pesanan
                if (empty($secretKey)) {
                    throw new \Exception('Kunci Xendit belum dipasang di file .env Laravel');
                }

                $response = Http::withBasicAuth($secretKey, '')
                    ->post('https://api.xendit.co/v2/invoices', [
                        'external_id' => $orderId,
                        'amount' => $totalAkhir,
                        'description' => 'Pembayaran Pesanan - ' . $orderId,
                    ]);

                if ($response->successful()) {
                    // Mengambil URL Web Pembayaran dari Xendit untuk digambar jadi QR di Flutter
                    $qrUrl = $response->json('invoice_url'); 
                } else {
                    throw new \Exception('Xendit Error: ' . $response->body());
                }
            }
            // ------------------------------

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => [
                    'pesanan_id' => $pesanan->id,
                    'total_akhir' => $totalAkhir,
                    'order_id' => $orderId,
                    'qr_url' => $qrUrl 
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. Mendapatkan Ringkasan Struk / Nota
     */
    public function getReceipt($id, Request $request)
    {
        $pesanan = Pesanan::with(['items.menu', 'user', 'pembayaran'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$pesanan) {
            return response()->json([

            
                'success' => false,
                'message' => 'Struk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pesanan
        ], 200);
    }

    /**
     * 3. Pengecekan Status Pembayaran (Mendukung Jalur Xendit & Backdoor)
     */
    public function cekStatusPembayaran($orderId)
    {
        try {
            $parts = explode('-', $orderId);
            $pesananId = $parts[1] ?? null;

            if (!$pesananId) {
                return response()->json(['status' => 'error', 'message' => 'Format Order ID tidak valid'], 400);
            }

            // 1. CEK DATABASE LOKAL DULU
            $pembayaranLokal = Pembayaran::where('pesanan_id', $pesananId)->first();
            if ($pembayaranLokal && $pembayaranLokal->status === 'paid') {
                return response()->json(['status' => 'paid', 'message' => 'Pembayaran Berhasil!']);
            }

            // 2. TANYA KE SERVER XENDIT INVOICE
            $secretKey = env('XENDIT_SECRET_KEY');
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
                ->get('https://api.xendit.co/v2/invoices?external_id=' . $orderId);

            if ($response->successful()) {
                $data = $response->json();
                $statusXendit = null;

                // Pengecekan cerdas: Apakah datanya array atau object tunggal?
                if (isset($data[0]) && isset($data[0]['status'])) {
                    $statusXendit = strtoupper($data[0]['status']);
                } elseif (isset($data['status'])) {
                    $statusXendit = strtoupper($data['status']);
                }

                // Jika status berhasil ditemukan di dalam data
                if ($statusXendit) {
                    // Kita tambahkan COMPLETED dan SUCCESS untuk jaga-jaga
                    if (in_array($statusXendit, ['PAID', 'SETTLED', 'COMPLETED', 'SUCCESS'])) {
                        if ($pembayaranLokal) {
                            $pembayaranLokal->update([
                                'status' => 'paid',
                                'dibayar_at' => now()
                            ]);
                        }
                        Pesanan::where('id', $pesananId)->update(['status' => 'processing']);

                        return response()->json(['status' => 'paid', 'message' => 'Pembayaran Berhasil!']);
                    }
                    
                    // Jika Xendit bilang belum lunas, tampilkan status ASLINYA ke layar!
                    return response()->json(['status' => 'unpaid', 'message' => 'Belum Lunas. (Status Xendit: ' . $statusXendit . ')']);
                }
                
                // Jika formatnya benar-benar aneh, cetak isi mentahnya ke layar
                return response()->json(['status' => 'unpaid', 'message' => 'Data Xendit Aneh: ' . substr(json_encode($data), 0, 100)]);
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal mengecek ke Xendit'], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}