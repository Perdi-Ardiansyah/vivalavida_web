<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsDashboard extends Component
{
    public $filter = '7_hari'; // default filter

    public function setFilter($range)
    {
        $this->filter = $range;
    }

    public function render()
    {
        // 1. Tentukan Range Waktu
        $startDate = Carbon::now();
        if ($this->filter == 'hari_ini') {
            $startDate = Carbon::today();
        } elseif ($this->filter == '7_hari') {
            $startDate = Carbon::now()->subDays(7);
        } elseif ($this->filter == 'bulan_ini') {
            $startDate = Carbon::now()->startOfMonth();
        }

        // 2. Data KPI (Kartu Atas)
        $totalPendapatan = DB::table('pesanans')
            ->where('status', 'completed')
            ->where('updated_at', '>=', $startDate)
            ->sum('total_akhir');

        $totalPesanan = DB::table('pesanans')
            ->where('status', 'completed')
            ->where('updated_at', '>=', $startDate)
            ->count();

        $rataRataPesanan = $totalPesanan > 0 ? $totalPendapatan / $totalPesanan : 0;

        $customerBaru = DB::table('users')
            ->where('created_at', '>=', $startDate)
            ->count();

        // 3. Data Tren Penjualan (7 Hari Terakhir untuk Grafik)
        $trenPenjualan = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = DB::table('pesanans')
                ->where('status', 'completed')
                ->whereDate('updated_at', $date)
                ->sum('total_akhir');
            
            $trenPenjualan[] = [
                'hari' => $date->isoFormat('ddd'),
                'nilai' => $revenue,
                // Hitung tinggi bar (maks 100%) untuk CSS
                'tinggi' => $totalPendapatan > 0 ? ($revenue / ($totalPendapatan / 2)) * 100 : 0
            ];
        }

        // 4. Metode Pembayaran (Doughnut Chart)
        $qrisCount = DB::table('pesanans')->where('status', 'completed')->where('updated_at', '>=', $startDate)->where('metode_pembayaran', 'qris')->count();
        $tunaiCount = DB::table('pesanans')->where('status', 'completed')->where('updated_at', '>=', $startDate)->where('metode_pembayaran', 'tunai')->count();
        $totalBayar = ($qrisCount + $tunaiCount) ?: 1;
        
        $persenQris = ($qrisCount / $totalBayar) * 100;
        $persenTunai = ($tunaiCount / $totalBayar) * 100;

        // 5. Produk Terlaris
        $produkTerlaris = DB::table('pesanan_items')
            ->join('pesanans', 'pesanan_items.pesanan_id', '=', 'pesanans.id')
            ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
            ->join('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
            ->select(
                'menus.nama',
                'menus.gambar',
                'kategori_menus.nama as kategori',
                DB::raw('SUM(pesanan_items.jumlah) as total_terjual'),
                DB::raw('SUM(pesanan_items.jumlah * pesanan_items.harga_satuan) as total_pendapatan')
            )
            ->where('pesanans.status', 'completed')
            ->where('pesanans.updated_at', '>=', $startDate)
            ->groupBy('menus.id', 'menus.nama', 'menus.gambar', 'kategori_menus.nama')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.analytics-dashboard', [
            'totalPendapatan' => $totalPendapatan,
            'totalPesanan' => $totalPesanan,
            'rataRataPesanan' => $rataRataPesanan,
            'customerBaru' => $customerBaru,
            'trenPenjualan' => $trenPenjualan,
            'persenQris' => $persenQris,
            'persenTunai' => $persenTunai,
            'produkTerlaris' => $produkTerlaris,
        ]);
    }
}