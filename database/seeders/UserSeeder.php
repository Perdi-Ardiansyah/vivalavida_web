<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin Utama
        User::create([
            'name' => 'Perdi Ardiansyah',
            'email' => 'admin@vivalavida.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '08111111111',
            'poin' => 0,
        ]);

        // Akun Kasir
        User::create([
            'name' => 'Siska Kasir',
            'email' => 'kasir@vivalavida.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
            'phone' => '08222222222',
            'poin' => 0,
        ]);

        // Akun Pelanggan (Dummy untuk pesanan dari App)
        User::create([
            'name' => 'Aditya Wijaya',
            'email' => 'aditya@email.com',
            'password' => Hash::make('pelanggan123'),
            'role' => 'pelanggan',
            'phone' => '08333333333',
            'poin' => 150,
        ]);
    }
}