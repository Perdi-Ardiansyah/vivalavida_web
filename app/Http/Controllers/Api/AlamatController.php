<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlamatPelanggan;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    /**
     * Menampilkan semua alamat milik pelanggan
     */
    public function index(Request $request)
    {
        $addresses = AlamatPelanggan::where('user_id', $request->user()->id)->get();

        return response()->json([
            'success' => true,
            'data' => $addresses
        ], 200);
    }

    /**
     * Menyimpan Alamat Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'label_alamat' => 'required|string|max:255', // Rumah / Kantor / Apartemen
            'alamat_lengkap' => 'required|string',
            'catatan_kurir' => 'nullable|string',
        ]);

        // Jika alamat pertama, set sebagai utama otomatis
        $isFirst = AlamatPelanggan::where('user_id', $request->user()->id)->count() === 0;

        $alamat = AlamatPelanggan::create([
            'user_id' => $request->user()->id,
            'label_alamat' => $request->label_alamat,
            'alamat_lengkap' => $request->alamat_lengkap,
            'catatan_kurir' => $request->catatan_kurir,
            'is_utama' => $isFirst,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat baru berhasil ditambahkan',
            'data' => $alamat
        ], 201);
    }

    /**
     * Memperbarui Alamat (Aksi tombol "Ubah" di UI)
     */
    public function update(Request $request, $id)
    {
        $alamat = AlamatPelanggan::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'label_alamat' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'catatan_kurir' => 'nullable|string',
        ]);

        $alamat->update($request->only(['label_alamat', 'alamat_lengkap', 'catatan_kurir']));

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil diperbarui',
            'data' => $alamat
        ], 200);
    }

    /**
     * Menghapus Alamat (Aksi tombol "Hapus" di UI)
     */
    public function destroy(Request $request, $id)
    {
        $alamat = AlamatPelanggan::where('user_id', $request->user()->id)->findOrFail($id);
        $alamat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil dihapus'
        ], 200);
    }
}