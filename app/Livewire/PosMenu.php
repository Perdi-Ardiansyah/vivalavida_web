<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PosMenu extends Component
{
    public $search = '';
    public $kategoriFilter = 'semua';
    
    public $cart = [];
    public $namaPelanggan = '';
    public $tipePesanan = 'dine_in';
    public $diskon = 0;

    // FUNGSI YANG DIPERBAIKI: Hanya butuh ID, sisanya dicari otomatis
    public function addToCart($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
        } else {
            $menu = DB::table('menus')->where('id', $id)->first();
            if ($menu) {
                $this->cart[$id] = [
                    'id' => $menu->id,
                    'nama' => $menu->nama,
                    'harga' => $menu->harga,
                    'qty' => 1,
                    'gambar' => $menu->gambar
                ];
            }
        }
    }

    public function incrementQty($id)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['qty']++;
        }
    }

    public function decrementQty($id)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['qty'] > 1) {
                $this->cart[$id]['qty']--;
            } else {
                unset($this->cart[$id]);
            }
        }
    }

    public function prosesPesanan()
    {
        if (count($this->cart) === 0) {
            return;
        }

        try {
            $subtotal = collect($this->cart)->sum(function($item) {
                return $item['harga'] * $item['qty'];
            });
            
            $pajak = $subtotal * 0.11;
            $totalAkhir = $subtotal + $pajak - $this->diskon;

            // Logika baru: user_id bisa NULL untuk guest, sumber_pesanan pasti 'kasir'
            $pesananId = DB::table('pesanans')->insertGetId([
                'user_id' => null, // Dikosongkan untuk tamu (guest)
                'nama_pelanggan' => $this->namaPelanggan ?: 'Guest Customer',
                'tipe_pesanan' => $this->tipePesanan,
                'sumber_pesanan' => 'kasir', // Pastikan ini 'kasir'
                'status' => 'new',
                'status_pembayaran' => 'belum_bayar',
                'total_harga' => $subtotal,
                'total_akhir' => $totalAkhir,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemsData = [];
            foreach ($this->cart as $item) {
                $itemsData[] = [
                    'pesanan_id' => $pesananId,
                    'menu_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                ];
            }
            DB::table('pesanan_items')->insert($itemsData);

            $this->cart = [];
            $this->namaPelanggan = '';
            
            // Redirect ke panel kasir
            return redirect('/kasir/pos');

        } catch (\Exception $e) {
            session()->flash('error_pesanan', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $kategoris = DB::table('kategori_menus')->get();

        $query = DB::table('menus')->leftJoin('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
                    ->select('menus.*', 'kategori_menus.nama as nama_kategori');

        if (!empty($this->search)) {
            $query->where('menus.nama', 'like', '%' . $this->search . '%');
        }

        if ($this->kategoriFilter !== 'semua') {
            $query->where('menus.kategori_id', $this->kategoriFilter);
        }

        $menus = $query->get();

        $subtotal = collect($this->cart)->sum(function($item) {
            return $item['harga'] * $item['qty'];
        });
        $pajak = $subtotal * 0.11;
        $totalBayar = $subtotal + $pajak - $this->diskon;

        return view('livewire.pos-menu', [
            'kategoris' => $kategoris,
            'menus' => $menus,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'totalBayar' => $totalBayar
        ]);
    }
}