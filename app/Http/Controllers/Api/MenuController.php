<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * 1. Mendapatkan semua kategori menu (Espresso, Manual Brew, dll)
     */
    public function getCategories()
    {
        $categories = KategoriMenu::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    /**
     * 2. Mendapatkan daftar menu (Bisa difilter berdasarkan kategori atau pencarian nama)
     */
    public function getMenus(Request $request)
    {
        // Hanya ambil menu yang statusnya 'tersedia'
        $query = Menu::where('tersedia', true);

        // Jika Flutter mengirimkan parameter kategori_id (saat user klik tab kategori)
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Jika Flutter mengirimkan keyword pencarian (saat user mengetik di search bar)
        if ($request->has('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Ambil data menu beserta nama kategorinya
        $menus = $query->with('kategori')->get();

        return response()->json([
            'success' => true,
            'data' => $menus
        ], 200);
    }

    /**
     * 3. Mendapatkan detail satu produk (Untuk halaman product_detail_screen.dart)
     */
    public function getMenuDetail($id)
    {
        $menu = Menu::with('kategori')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $menu
        ], 200);
    }
}