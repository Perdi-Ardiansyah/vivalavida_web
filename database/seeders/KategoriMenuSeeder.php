<?php

namespace Database\Seeders;

use App\Models\KategoriMenu;
use Illuminate\Database\Seeder;

class KategoriMenuSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Espresso Based', 'deskripsi' => 'Minuman kopi berbahan dasar espresso standar'],
            ['nama' => 'Manual Brew', 'deskripsi' => 'Kopi seduh manual dengan biji pilihan'],
            ['nama' => 'Non-Coffee', 'deskripsi' => 'Minuman segar tanpa kafein'],
            ['nama' => 'Pastry & Food', 'deskripsi' => 'Camilan dan makanan berat'],
        ];

        foreach ($kategori as $item) {
            KategoriMenu::create($item);
        }
    }
}