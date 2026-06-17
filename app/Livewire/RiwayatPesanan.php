<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RiwayatPesanan extends Component
{
    public $search = '';
    public $statusFilter = 'semua'; // semua, completed, cancelled

    // Variabel untuk Modal Detail/Struk
    public $showModalStruk = false;
    public $pesananStruk = null;
    public $modalSubtotal = 0;
    public $modalPajak = 0;

    public function bukaModalStruk($id)
    {
        $this->pesananStruk = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->where('pesanans.id', $id)
            ->first();

        if ($this->pesananStruk) {
            $this->pesananStruk->items = DB::table('pesanan_items')
                ->leftJoin('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->select('pesanan_items.*', 'menus.nama')
                ->where('pesanan_items.pesanan_id', $id)
                ->get();

            $this->modalSubtotal = ceil($this->pesananStruk->total_akhir / 1.1);
            $this->modalPajak = $this->pesananStruk->total_akhir - $this->modalSubtotal;

            $this->showModalStruk = true;
        }
    }

    public function tutupModalStruk()
    {
        $this->showModalStruk = false;
        $this->pesananStruk = null;
    }

    // --- FUNGSI BARU: EKSPOR LAPORAN CSV ---
    public function exportCSV()
    {
        // 1. Ambil data sesuai filter yang sedang aktif
        $query = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->whereIn('pesanans.status', ['completed', 'cancelled']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.nama_pelanggan', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'semua') {
            $query->where('pesanans.status', $this->statusFilter);
        }

        // Ambil semua data (tanpa limit) untuk laporan
        $pesanans = $query->orderBy('pesanans.updated_at', 'desc')->get();

        // 2. Generate file CSV secara dinamis
        $fileName = 'laporan-riwayat-viva-' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($pesanans) {
            $file = fopen('php://output', 'w');
            
            // Header Kolom di Excel
            fputcsv($file, ['ID Pesanan', 'Tanggal Transaksi', 'Nama Pelanggan', 'Tipe Pesanan', 'Metode Pembayaran', 'Total Harga (Rp)', 'Status']);

            // Isi Data
            foreach ($pesanans as $pesanan) {
                fputcsv($file, [
                    '#VV-' . str_pad($pesanan->id, 3, '0', STR_PAD_LEFT),
                    \Carbon\Carbon::parse($pesanan->updated_at)->format('Y-m-d H:i:s'),
                    $pesanan->nama_pelanggan ?? 'Walk-in Customer',
                    str_replace('_', ' ', strtoupper($pesanan->tipe_pesanan)),
                    strtoupper($pesanan->metode_pembayaran ?? 'TUNAI'),
                    $pesanan->total_akhir,
                    strtoupper($pesanan->status)
                ]);
            }
            fclose($file);
        }, $fileName);
    }

    public function render()
    {
        $query = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->whereIn('pesanans.status', ['completed', 'cancelled']); 

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.nama_pelanggan', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'semua') {
            $query->where('pesanans.status', $this->statusFilter);
        }

        $pesanans = $query->orderBy('pesanans.updated_at', 'desc')->limit(50)->get();

        foreach ($pesanans as $pesanan) {
            $pesanan->items = DB::table('pesanan_items')
                ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->select('menus.nama', 'pesanan_items.jumlah')
                ->where('pesanan_items.pesanan_id', $pesanan->id)
                ->get();
        }

        return view('livewire.riwayat-pesanan', compact('pesanans'));
    }
}