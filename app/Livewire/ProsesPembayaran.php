<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ProsesPembayaran extends Component
{
    public $pesanan_id;
    public $pesanan;
    public $items = [];
    
    public $uang_diterima = '';
    public $kembalian = 0;

    public $subtotal = 0;
    public $pajak = 0;
    public $total = 0;

    // Tambahan untuk layar sukses
    public $is_success = false; 
    public $waktu_selesai;

    public function mount($pesananId)
    {
        $this->pesanan_id = $pesananId;
        
        $this->pesanan = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', 'users.name as nama_pelanggan')
            ->where('pesanans.id', $pesananId)
            ->first();
        
        $this->items = DB::table('pesanan_items')
            ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
            // Tambahkan menus.gambar ke dalam select
            ->select('pesanan_items.*', 'menus.nama', 'menus.gambar') 
            ->where('pesanan_id', $pesananId)
            ->get();

        $this->total = $this->pesanan->total_akhir;
        $this->subtotal = ceil($this->total / 1.1); 
        $this->pajak = $this->total - $this->subtotal;
    }

    public function updatedUangDiterima()
    {
        $nominal = (int) str_replace('.', '', $this->uang_diterima);
        
        if ($nominal >= $this->total) {
            $this->kembalian = $nominal - $this->total;
        } else {
            $this->kembalian = 0;
        }
    }

    public function setUangDiterima($nominal)
    {
        $this->uang_diterima = $nominal;
        $this->kembalian = $nominal - $this->total;
    }

    public function konfirmasiPembayaran()
    {
        $nominal = (int) str_replace('.', '', $this->uang_diterima);
        
        if ($nominal < $this->total) {
            return; 
        }

        DB::table('pesanans')->where('id', $this->pesanan_id)->update([
            'status_pembayaran' => 'sudah_bayar',
            'metode_pembayaran' => 'Tunai', 
            'status' => 'preparing',
            'updated_at' => now()
        ]);

        // Rekam waktu selesai dan ubah layar menjadi struk sukses
        $this->waktu_selesai = now()->translatedFormat('d M Y, H:i');
        $this->is_success = true;
    }

    // Fungsi untuk tombol kembali ke antrean awal
    public function pesananBaru()
    {
        return redirect()->route('kasir.pos');
    }

    public function render()
    {
        return view('livewire.proses-pembayaran');
    }
}