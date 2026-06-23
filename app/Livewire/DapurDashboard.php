<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DapurDashboard extends Component
{
    public function mulaiMasak($id)
    {
        DB::table('pesanans')->where('id', $id)->update([
            'status_makanan' => 'sedang_dimasak',
            'updated_at' => now()
        ]);
    }

    public function makananSelesai($id)
    {
        // 1. Tandai pekerjaan Dapur selesai
        DB::table('pesanans')->where('id', $id)->update([
            'status_makanan' => 'selesai',
            'updated_at' => now()
        ]);

        // 2. Jalankan Pengecekan Cerdas
        $this->cekSemuaSelesai($id);
    }

    private function cekSemuaSelesai($id)
    {
        $pesanan = DB::table('pesanans')->where('id', $id)->first();

        // Cek apakah pesanan ini ADA item MINUMAN?
        $adaMinuman = DB::table('pesanan_items')
            ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
            ->join('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
            ->where('pesanan_items.pesanan_id', $id)
            ->where('kategori_menus.tipe', 'minuman')
            ->exists();

        // Syarat pesanan siap (ready):
        // 1. Makanan pasti sudah selesai
        // 2. Minuman harus selesai (JIKA ADA), ATAU memang tidak pesan minuman sama sekali (! $adaMinuman)
        $minumanSelesai = (!$adaMinuman || $pesanan->status_dapur === 'selesai');

        if ($minumanSelesai) {
            DB::table('pesanans')->where('id', $id)->update(['status' => 'ready']);

            // --- LOGIKA PEMBUATAN NOTIFIKASI DITAMBAHKAN DI SINI ---
            // Pastikan pesanan ini dipesan lewat aplikasi (punya user_id)
            if ($pesanan->user_id != null) {
                DB::table('notifikasis')->insert([
                    'user_id'   => $pesanan->user_id,
                    'tipe'      => 'pesanan', // Tipe untuk menentukan ikon di Flutter
                    'judul'     => 'Pesanan Sudah Siap! 🎉',
                    'deskripsi' => 'Pesanan Anda (Order #' . $id . ') telah selesai disiapkan. Silakan ambil di konter ya!',
                    'is_read'   => 0, // Belum dibaca
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ]);
            }
            // --------------------------------------------------------
        }
    }

    public function render()
    {
        // Tampilkan HANYA pesanan yang memiliki item kategori MAKANAN
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
                      ->where('kategori_menus.tipe', 'makanan');
            })
            ->orderBy('pesanans.created_at', 'asc')
            ->get();

        foreach ($pesanans as $pesanan) {
            // Tampilkan hanya item MAKANAN di layar Dapur
            $pesanan->items = DB::table('pesanan_items')
                ->join('menus', 'pesanan_items.menu_id', '=', 'menus.id')
                ->join('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
                ->select('pesanan_items.*', 'menus.nama')
                ->where('pesanan_items.pesanan_id', $pesanan->id)
                ->where('kategori_menus.tipe', 'makanan')
                ->get();
        }

        return view('livewire.dapur-dashboard', [
            'pesanans' => $pesanans,
        ]);
    }
}