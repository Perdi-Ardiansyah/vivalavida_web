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

    // FUNGSI YANG DIPERBAIKI: Menambahkan inisialisasi catatan
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
                    'gambar' => $menu->gambar,
                    'catatan' => '' // <-- Tambahkan ini agar input kasir tidak error
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
            
            // Mengambil pajak dari tabel pengaturans secara dinamis (Sama seperti Flutter)
            $pengaturan = DB::table('pengaturans')->first();
            $taxRate = $pengaturan ? ($pengaturan->pajak_persen / 100) : 0.11;

            $pajak = $subtotal * $taxRate;
            $totalAkhir = $subtotal + $pajak - $this->diskon;

            $pesananId = DB::table('pesanans')->insertGetId([
                'user_id' => null, 
                'nama_pelanggan' => $this->namaPelanggan ?: 'Guest Customer',
                'tipe_pesanan' => $this->tipePesanan,
                'sumber_pesanan' => 'kasir', 
                
                // --- UBAH DUA BARIS INI ---
                'status' => 'ready', // Ubah ke 'diproses' atau 'disiapkan' (sesuaikan dengan kata yang dipakai di panel dapurmu)
                'status_pembayaran' => 'sudah_bayar', // Ubah ke 'sudah_bayar' atau 'lunas'
                // --------------------------

                'total_harga' => $subtotal,
                'total_akhir' => $totalAkhir,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemsData = [];
            foreach ($this->cart as $item) {
                // 1. Siapkan Array untuk Catatan/Opsi Tambahan
                $opsiArray = [];
                if (isset($item['catatan']) && trim($item['catatan']) !== '') {
                    $opsiArray[] = trim($item['catatan']);
                }

                $itemsData[] = [
                    'pesanan_id' => $pesananId,
                    'menu_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    // 2. Karena menggunakan DB::table insert, pastikan array diubah ke format JSON
                    'opsi_tambahan' => !empty($opsiArray) ? json_encode($opsiArray) : null,
                ];
            }
            DB::table('pesanan_items')->insert($itemsData);
            
            $this->cart = [];
            $this->namaPelanggan = '';
            

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

        // Ambil pengaturan pajak untuk ditampilkan
        $pengaturan = DB::table('pengaturans')->first();
        $pajakPersen = $pengaturan ? $pengaturan->pajak_persen : 11;
        $taxRate = $pajakPersen / 100;
        
        $pajak = $subtotal * $taxRate;
        $totalBayar = $subtotal + $pajak - $this->diskon;

        return view('livewire.pos-menu', [
            'kategoris' => $kategoris,
            'menus' => $menus,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'pajakPersen' => $pajakPersen, // <- Kirim variabel ini agar teks di Blade bisa dinamis
            'totalBayar' => $totalBayar
        ]);
    }
}