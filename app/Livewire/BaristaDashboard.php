<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BaristaDashboard extends Component
{
    public function mulaiBuat($id)
    {
        DB::table('pesanans')->where('id', $id)->update([
            'status_dapur' => 'sedang_dibuat',
            'updated_at' => now()
        ]);
    }

    public function minumanSelesai($id)
    {
        // 1. Tandai pekerjaan Barista selesai
        DB::table('pesanans')->where('id', $id)->update([
            'status_dapur' => 'selesai',
            'updated_at' => now()
        ]);

        // 2. Jalankan Pengecekan Cerdas
        $this->cekSemuaSelesai($id);
    }

    private function cekSemuaSelesai($id)
    {
        $pesanan = DB::table('pesanans')->where('id', $id)->first();

        // Cek apakah pesanan ini ADA item MAKANAN?
        $adaMakanan = DB::table('pesanan_items')
            ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
            ->join('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
            ->where('pesanan_items.pesanan_id', $id)
            ->where('kategori_menus.tipe', 'makanan')
            ->exists();

        // Syarat Kasir dipanggil (ready):
        // 1. Minuman pasti sudah selesai (karena fungsi ini dipanggil setelah tombol ditekan)
        // 2. Makanan harus selesai (JIKA ADA), ATAU memang tidak ada makanan sama sekali (! $adaMakanan)
        $makananSelesai = (!$adaMakanan || $pesanan->status_makanan === 'selesai');

        if ($makananSelesai) {
            DB::table('pesanans')->where('id', $id)->update(['status' => 'ready']);
        }
    }

    public function render()
    {
        // Tampilkan HANYA pesanan yang memiliki item kategori MINUMAN
        $pesanans = DB::table('pesanans')
            ->leftJoin('users', 'pesanans.user_id', '=', 'users.id')
            ->select('pesanans.*', 'users.name as nama_pelanggan')
            ->where('pesanans.status', 'preparing')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('pesanan_items')
                      ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                      ->join('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
                      ->whereColumn('pesanan_items.pesanan_id', 'pesanans.id')
                      ->where('kategori_menus.tipe', 'minuman');
            })
            ->orderBy('pesanans.created_at', 'asc')
            ->get();

        $countMenunggu = 0;
        $countSedangDibuat = 0;

        foreach ($pesanans as $pesanan) {
            if ($pesanan->status_dapur === 'sedang_dibuat') {
                $countSedangDibuat++;
            } else {
                $countMenunggu++;
            }

            // Tampilkan hanya item MINUMAN di layar Barista
            $pesanan->items = DB::table('pesanan_items')
                ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->join('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
                ->select('pesanan_items.*', 'menus.nama')
                ->where('pesanan_items.pesanan_id', $pesanan->id)
                ->where('kategori_menus.tipe', 'minuman')
                ->get();
        }

        return view('livewire.barista-dashboard', [
            'pesanans' => $pesanans,
            'countMenunggu' => $countMenunggu,
            'countSedangDibuat' => $countSedangDibuat,
        ]);
    }
}