<?php

namespace Database\Seeders;

use App\Models\Meja;
use Illuminate\Database\Seeder;

class MejaSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Meja::create([
                'nomor' => 'T-' . str_pad($i, 2, '0', STR_PAD_LEFT), // T-01, T-02, dst.
                'kapasitas' => ($i <= 5) ? 2 : 4, // Meja 1-5 untuk 2 orang, sisanya 4 orang
                'status' => 'tersedia',
            ]);
        }
    }
}