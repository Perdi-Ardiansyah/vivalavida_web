<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Ambil data yang dikirim oleh Xendit
        $external_id = $request->input('external_id'); // Format: ORDER-72
        $status = $request->input('status'); // Contoh: PAID
        $payment_method = $request->input('payment_method'); 

        // 2. Ekstrak ID Pesanan (Membuang teks 'ORDER-')
        $pesananId = str_replace('ORDER-', '', $external_id);

        // 3. Catat di log Laravel (opsional, untuk debugging)
        Log::info('Webhook Xendit Masuk:', ['id' => $pesananId, 'status' => $status]);

        // 4. Jika Pembayaran Berhasil, Update Database!
        if ($status === 'PAID') {
            DB::table('pesanans')->where('id', $pesananId)->update([
                'status_pembayaran' => 'sudah_bayar',
                'metode_pembayaran' => $payment_method ?? 'ONLINE',
                'status' => 'preparing', // Langsung lempar pesanan ini ke layar Dapur!
                'updated_at' => now()
            ]);
        }

        // Xendit mewajibkan kita membalas HTTP 200 agar mereka tahu sistem kita tidak mati
        return response()->json(['message' => 'Webhook received successfully'], 200);
    }
}