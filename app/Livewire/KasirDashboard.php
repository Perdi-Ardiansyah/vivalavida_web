<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class KasirDashboard extends Component
{
    // Properti Filter
    public $search = '';
    public $statusFilter = 'semua';
    public $tipeFilter = 'semua';

    // Variabel Modal Verifikasi
    public $showModalVerifikasi = false;
    public $pesananVerifikasi = null;
    
    // Variabel Modal Detail/Struk
    public $showModalStruk = false;
    public $pesananStruk = null;
    public $modalSubtotal = 0;
    public $modalPajak = 0;

    // Variabel Modal Pembayaran Kasir
    public $showModalPembayaran = false;
    public $pesananPembayaran = null;
    public $uangDiterima = '';
    public $kembalian = 0;
    public $totalPembayaran = 0;

    public function bukaModalPembayaran($id)
    {
        $this->pesananPembayaran = DB::table('pesanans')->where('id', $id)->first();

        if ($this->pesananPembayaran) {
            $this->totalPembayaran = $this->pesananPembayaran->total_akhir;
            $this->uangDiterima = '';
            $this->kembalian = 0;
            $this->showModalPembayaran = true;
        }
    }

    public function tutupModalPembayaran()
    {
        $this->showModalPembayaran = false;
        $this->pesananPembayaran = null;
    }

    public function updatedUangDiterima()
    {
        $nominal = (int) str_replace('.', '', $this->uangDiterima);
        $this->kembalian = $nominal >= $this->totalPembayaran ? $nominal - $this->totalPembayaran : 0;
    }

    public function setUangDiterima($nominal)
    {
        $this->uangDiterima = $nominal;
        $this->kembalian = $nominal >= $this->totalPembayaran ? $nominal - $this->totalPembayaran : 0;
    }

    public function konfirmasiPembayaran()
    {
        $nominal = (int) str_replace('.', '', $this->uangDiterima);
        if ($nominal < $this->totalPembayaran) return; 

        DB::table('pesanans')->where('id', $this->pesananPembayaran->id)->update([
            'status_pembayaran' => 'sudah_bayar',
            'metode_pembayaran' => 'tunai', 
            'status' => 'preparing',
            'updated_at' => now()
        ]);

        $idPesananSelesai = $this->pesananPembayaran->id;
        $this->tutupModalPembayaran();
        $this->bukaModalStruk($idPesananSelesai);
    }

    public function bukaModalVerifikasi($id)
    {
        $this->pesananVerifikasi = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            // PERBAIKAN 1: Mencegah nama pelanggan tertimpa menjadi NULL
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->where('pesanans.id', $id)
            ->first();

        if ($this->pesananVerifikasi) {
            $this->pesananVerifikasi->items = DB::table('pesanan_items')
                ->leftJoin('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->leftJoin('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
                ->select('pesanan_items.*', 'menus.nama', 'menus.gambar', 'kategori_menus.tipe')
                ->where('pesanan_items.pesanan_id', $id)
                ->get();

            $this->modalSubtotal = ceil($this->pesananVerifikasi->total_akhir / 1.1);
            $this->modalPajak = $this->pesananVerifikasi->total_akhir - $this->modalSubtotal;
            $this->showModalVerifikasi = true;
        }
    }

    public function tutupModal()
    {
        $this->showModalVerifikasi = false;
        $this->pesananVerifikasi = null;
    }

    public function teruskanKeDapur($id)
    {
        DB::table('pesanans')->where('id', $id)->update([
            'status' => 'preparing',
            'updated_at' => now()
        ]);
        $this->tutupModal();
    }

    public function tolakPesanan($id)
    {
        DB::table('pesanans')->where('id', $id)->update([
            'status' => 'cancelled',
            'updated_at' => now()
        ]);
        $this->tutupModal();
    }

    public function pesananSelesai($id)
    {
        DB::table('pesanans')->where('id', $id)->update([
            'status' => 'completed',
            'updated_at' => now()
        ]);
    }

    public function bukaModalStruk($id)
    {
        $this->pesananStruk = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            // PERBAIKAN 2
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

    public function render()
    {
        $query = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            // PERBAIKAN 3
            ->select('pesanans.*', DB::raw('COALESCE(pesanans.nama_pelanggan, users.name) as nama_pelanggan'))
            ->whereNotIn('pesanans.status', ['completed', 'cancelled']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                // PERBAIKAN 4: Menambahkan pencarian berdasarkan nama pelanggan manual agar bisa dicari di search bar
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.nama_pelanggan', 'like', '%' . $this->search . '%')
                  ->orWhere('pesanans.id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->tipeFilter !== 'semua') {
            $query->where('pesanans.tipe_pesanan', $this->tipeFilter);
        }

        if ($this->statusFilter === 'belum_bayar') {
            $query->where(function($q) {
                $q->whereNotIn(DB::raw('LOWER(status_pembayaran)'), ['sudah_bayar', 'paid', 'lunas'])
                  ->orWhereNull('status_pembayaran');
            });
        } elseif ($this->statusFilter === 'perlu_verifikasi') {
            $query->whereIn(DB::raw('LOWER(status_pembayaran)'), ['sudah_bayar', 'paid', 'lunas'])
                  ->where('pesanans.status', 'new');
        } elseif ($this->statusFilter === 'sedang_disiapkan') {
            $query->whereIn(DB::raw('LOWER(status_pembayaran)'), ['sudah_bayar', 'paid', 'lunas'])
                  ->where('pesanans.status', 'preparing');
        } elseif ($this->statusFilter === 'siap_diambil') {
            $query->where('pesanans.status', 'ready');
        }

        $pesanans = $query->orderBy('pesanans.created_at', 'desc')->get();

        foreach ($pesanans as $pesanan) {
            $items = DB::table('pesanan_items')
                ->leftJoin('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->leftJoin('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
                ->select('pesanan_items.*', 'menus.nama', 'kategori_menus.tipe')
                ->where('pesanan_items.pesanan_id', $pesanan->id)
                ->get();

            $pesanan->items = $items;
            $pesanan->has_makanan = $items->contains('tipe', 'makanan');
            $pesanan->has_minuman = $items->contains('tipe', 'minuman');
        }

        return view('livewire.kasir-dashboard', compact('pesanans'));
    }
}