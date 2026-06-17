<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderManagement extends Component
{
    use WithPagination;

    // Filter Properties
    public $search = '';
    public $dateRange = 'all'; // all, today, 7_days, 30_days
    public $statusFilter = 'all';
    public $sourceFilter = 'all';

    // Reset pagination ketika filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingDateRange() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingSourceFilter() { $this->resetPage(); }

    public function exportCSV()
    {
        // Fungsi ekspor sederhana (mirip dengan yang ada di Laporan Analitik)
        $query = $this->buildQuery();
        $pesanans = $query->orderBy('pesanans.created_at', 'desc')->get();

        $fileName = 'order-management-' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($pesanans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Customer', 'Items', 'Total', 'Payment', 'Source', 'Status', 'Date']);

            foreach ($pesanans as $order) {
                fputcsv($file, [
                    '#VC-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    $order->nama_pelanggan,
                    $order->total_items . ' Items',
                    $order->total_akhir,
                    strtoupper($order->metode_pembayaran ?? 'CASH'),
                    $order->tipe_pesanan,
                    strtoupper($order->status),
                    $order->created_at
                ]);
            }
            fclose($file);
        }, $fileName);
    }

    private function buildQuery()
    {
        $query = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select(
                'pesanans.*', 
                DB::raw('COALESCE(pesanans.nama_pelanggan, users.name, "Walk-in Customer") as nama_pelanggan'),
                // Subquery untuk menghitung jumlah item per pesanan
                DB::raw('(SELECT SUM(jumlah) FROM pesanan_items WHERE pesanan_items.pesanan_id = pesanans.id) as total_items')
            );

        // Filter Pencarian
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.nama_pelanggan', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.id', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Waktu
        if ($this->dateRange === 'today') {
            $query->whereDate('pesanans.created_at', Carbon::today());
        } elseif ($this->dateRange === '7_days') {
            $query->where('pesanans.created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($this->dateRange === '30_days') {
            $query->where('pesanans.created_at', '>=', Carbon::now()->subDays(30));
        }

        // Filter Status
        if ($this->statusFilter !== 'all') {
            $query->where('pesanans.status', $this->statusFilter);
        }

        // Filter Sumber (Dine-in / Takeaway / App)
        if ($this->sourceFilter !== 'all') {
            $query->where('pesanans.tipe_pesanan', $this->sourceFilter);
        }

        return $query;
    }

    public function render()
    {
        // 1. Data Tabel Utama dengan Paginasi (10 per halaman)
        $orders = $this->buildQuery()->orderBy('pesanans.created_at', 'desc')->paginate(10);

        // 2. Metrik Kartu Bawah: Today's Revenue
        $todaysRevenue = DB::table('pesanans')
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_akhir');
            
        $yesterdaysRevenue = DB::table('pesanans')
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::yesterday())
            ->sum('total_akhir');
            
        $revenueGrowth = 0;
        if ($yesterdaysRevenue > 0) {
            $revenueGrowth = (($todaysRevenue - $yesterdaysRevenue) / $yesterdaysRevenue) * 100;
        }

        // 3. Metrik Kartu Bawah: Active Orders (Sedang disiapkan)
        $activeOrders = DB::table('pesanans')
            ->whereIn('status', ['new', 'preparing', 'ready'])
            ->count();

        // 4. Metrik Kartu Bawah: Success Rate (30 hari terakhir)
        $last30DaysTotal = DB::table('pesanans')->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $last30DaysCompleted = DB::table('pesanans')->where('status', 'completed')->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $successRate = $last30DaysTotal > 0 ? ($last30DaysCompleted / $last30DaysTotal) * 100 : 0;

        return view('livewire.order-management', compact(
            'orders', 'todaysRevenue', 'revenueGrowth', 'activeOrders', 'successRate'
        ));
    }
}