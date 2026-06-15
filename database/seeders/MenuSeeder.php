<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // Espresso Based (Kategori ID: 1)
            ['kategori_id' => 1, 'nama' => 'Caramel Macchiato', 'deskripsi' => 'Espresso dengan sirup karamel dan susu', 'harga' => 45000, 'tersedia' => true],
            ['kategori_id' => 1, 'nama' => 'Espresso Macchiato', 'deskripsi' => 'Espresso dengan sedikit buih susu', 'harga' => 38000, 'tersedia' => true],
            ['kategori_id' => 1, 'nama' => 'Iced Caffe Latte', 'deskripsi' => 'Espresso dingin dengan susu segar', 'harga' => 35000, 'tersedia' => true],
            
            // Manual Brew (Kategori ID: 2)
            ['kategori_id' => 2, 'nama' => 'Vivalavida Special Blend', 'deskripsi' => 'Kopi house blend khas kami', 'harga' => 42000, 'tersedia' => true],
            
            // Pastry & Food (Kategori ID: 4)
            ['kategori_id' => 4, 'nama' => 'Almond Croissant', 'deskripsi' => 'Croissant renyah dengan taburan almond', 'harga' => 32000, 'tersedia' => true],
            ['kategori_id' => 4, 'nama' => 'Butter Croissant', 'deskripsi' => 'Croissant mentega klasik', 'harga' => 28000, 'tersedia' => true],
            ['kategori_id' => 4, 'nama' => 'Truffle Fries', 'deskripsi' => 'Kentang goreng dengan minyak truffle', 'harga' => 45000, 'tersedia' => true],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}