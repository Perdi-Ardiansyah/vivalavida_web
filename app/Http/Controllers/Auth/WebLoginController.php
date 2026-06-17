<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebLoginController extends Controller
{
    // Menampilkan halaman login form yang baru saja kita buat
    public function create()
    {
        return view('auth.login'); 
    }

    // Memproses data login dari form
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek role untuk menentukan arah lemparan halaman
            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($role === 'kasir') {
                return redirect()->intended('/kasir/pos');
            } elseif ($role === 'dapur') {
                return redirect()->intended('/dapur/antrean');
            }

            // Jika pelanggan nyasar login ke web, tendang keluar
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akses ditolak. Pelanggan hanya bisa login melalui Aplikasi Mobile.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses logout web
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}