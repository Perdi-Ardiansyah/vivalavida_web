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
        // Menggunakan relasi alamatPelanggans
        $alamats = $request->user()->alamatPelanggans()->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $alamats]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label_alamat' => 'required|string|max:50',
            'alamat_lengkap' => 'required|string',
            'catatan_kurir' => 'nullable|string'
        ]);

        $alamat = $request->user()->alamatPelanggans()->create($validated);

        return response()->json(['success' => true, 'message' => 'Alamat ditambahkan', 'data' => $alamat], 201);
    }
    // (Lakukan hal yang sama untuk fungsi update)

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