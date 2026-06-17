<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboard extends Component
{
    public function render()
    {
        $bulanIni = Carbon::now()->startOfMonth();
        $bulanLalu = Carbon::now()->subMonth()->startOfMonth();

        // 1. TOP METRICS (Kartu Atas)
        $totalRevenue = DB::table('pesanans')->where('status', 'completed')->where('created_at', '>=', $bulanIni)->sum('total_akhir');
        $totalOrders = DB::table('pesanans')->where('status', 'completed')->where('created_at', '>=', $bulanIni)->count();
        $newCustomers = DB::table('users')->where('created_at', '>=', $bulanIni)->count();

        // Cari Produk No 1
        $topProductData = DB::table('pesanan_items')
            ->join('pesanans', 'pesanan_items.pesanan_id', '=', 'pesanans.id')
            ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
            ->select('menus.nama', DB::raw('SUM(pesanan_items.jumlah) as total_terjual'))
            ->where('pesanans.status', 'completed')
            ->where('pesanans.created_at', '>=', Carbon::today())
            ->groupBy('menus.id', 'menus.nama')
            ->orderBy('total_terjual', 'desc')
            ->first();

        // 2. ORDER SOURCE (Doughnut Chart Simulasi)
        $totalSource = DB::table('pesanans')->where('status', 'completed')->count() ?: 1;
        $appOrders = DB::table('pesanans')->where('status', 'completed')->where('sumber_pesanan', 'app')->count();
        $walkInOrders = DB::table('pesanans')->where('status', 'completed')->where('sumber_pesanan', 'kasir')->count();
        
        $pctApp = round(($appOrders / $totalSource) * 100);
        $pctWalkIn = round(($walkInOrders / $totalSource) * 100);

        // 3. PAYMENT METHODS
        $qris = DB::table('pesanans')->where('status', 'completed')->where('metode_pembayaran', 'qris')->count();
        $tunai = DB::table('pesanans')->where('status', 'completed')->where('metode_pembayaran', 'tunai')->count();
        $totalPay = ($qris + $tunai) ?: 1;
        $pctQris = round(($qris / $totalPay) * 100);
        $pctTunai = round(($tunai / $totalPay) * 100);

        // 4. RECENT ORDERS (Tabel)
        $recentOrders = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->orderBy('pesanans.created_at', 'desc')
            ->limit(6)
            ->get();

        foreach ($recentOrders as $order) {
            $order->items = DB::table('pesanan_items')
                ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->select('menus.nama', 'pesanan_items.jumlah')
                ->where('pesanan_items.pesanan_id', $order->id)
                ->get();
        }

        // 5. TOP PRODUCTS LIST (Kanan Bawah)
        $topProductsList = DB::table('pesanan_items')
            ->join('pesanans', 'pesanan_items.pesanan_id', '=', 'pesanans.id')
            ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
            ->select('menus.nama', 'menus.gambar', DB::raw('SUM(pesanan_items.jumlah) as terjual'), DB::raw('SUM(pesanan_items.jumlah * pesanan_items.harga_satuan) as pendapatan'))
            ->where('pesanans.status', 'completed')
            ->groupBy('menus.id', 'menus.nama', 'menus.gambar')
            ->orderBy('pendapatan', 'desc')
            ->limit(4)
            ->get();

        // 6. TREN CHART (7 Hari Terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $rev = DB::table('pesanans')->where('status', 'completed')->whereDate('updated_at', $date)->sum('total_akhir');
            $chartData[] = [
                'hari' => $date->isoFormat('ddd'),
                'rev' => $rev,
                'height' => $totalRevenue > 0 ? ($rev / ($totalRevenue/5)) * 100 : 0
            ];
        }

        return view('livewire.admin-dashboard', compact(
            'totalRevenue', 'totalOrders', 'newCustomers', 'topProductData', 
            'pctApp', 'pctWalkIn', 'pctQris', 'pctTunai', 
            'recentOrders', 'topProductsList', 'chartData'
        ));
    }

    // --- FUNGSI EKSPOR LAPORAN PENJUALAN ---
    public function exportReport()
    {
        $bulanIni = Carbon::now()->startOfMonth();

        // Ambil data semua pesanan yang selesai di bulan ini
        $pesanans = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->where('pesanans.status', 'completed')
            ->where('pesanans.created_at', '>=', $bulanIni)
            ->orderBy('pesanans.created_at', 'desc')
            ->get();

        $fileName = 'admin-sales-report-' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($pesanans) {
            $file = fopen('php://output', 'w');
            
            // Header Kolom di Excel
            fputcsv($file, ['ID Transaksi', 'Tanggal', 'Nama Pelanggan', 'Tipe Pesanan', 'Sumber Pesanan', 'Metode Bayar', 'Total Pendapatan (Rp)']);

            // Looping data ke dalam baris Excel
            foreach ($pesanans as $order) {
                fputcsv($file, [
                    '#ORD-' . $order->id,
                    \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i'),
                    $order->nama_pelanggan ?? 'Walk-in Customer',
                    strtoupper($order->tipe_pesanan),
                    strtoupper($order->sumber_pesanan),
                    strtoupper($order->metode_pembayaran ?? 'TUNAI'),
                    $order->total_akhir
                ]);
            }
            fclose($file);
        }, $fileName);
    }
}