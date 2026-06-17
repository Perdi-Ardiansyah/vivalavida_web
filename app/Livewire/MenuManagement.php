<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $kategoriFilter = 'semua';

    // Variabel Form Modal Produk
    public $showModal = false;
    public $isEditMode = false;
    public $menuId = null;
    public $nama, $kategori_id, $harga, $deskripsi;
    public $gambar; 
    public $gambarLama;

    // --- VARIABEL MODAL KATEGORI ---
    public $showKategoriModal = false;
    public $isEditKategoriMode = false;
    public $kategoriId = null;
    public $namaKategori = '';
    public $tipeKategori = 'minuman'; // default

    public function updatingSearch() { $this->resetPage(); }
    public function updatingKategoriFilter() { $this->resetPage(); }

    // ==========================================
    // LOGIKA PRODUK / MENU
    // ==========================================
    public function toggleStatus($id)
    {
        $menu = DB::table('menus')->where('id', $id)->first();
        if ($menu) {
            $statusBaru = $menu->tersedia == 1 ? 0 : 1; 
            DB::table('menus')->where('id', $id)->update([
                'tersedia' => $statusBaru,
                'updated_at' => now()
            ]);
        }
    }

    public function hapusMenu($id)
    {
        $menu = DB::table('menus')->where('id', $id)->first();
        if ($menu && $menu->gambar) {
            Storage::disk('public')->delete($menu->gambar);
        }
        DB::table('menus')->where('id', $id)->delete();
        session()->flash('message', 'Menu berhasil dihapus.');
    }

    public function bukaModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function editMenu($id)
    {
        $menu = DB::table('menus')->where('id', $id)->first();
        if ($menu) {
            $this->menuId = $menu->id;
            $this->nama = $menu->nama;
            $this->kategori_id = $menu->kategori_id;
            $this->harga = $menu->harga;
            $this->deskripsi = $menu->deskripsi;
            $this->gambarLama = $menu->gambar;
            
            $this->isEditMode = true;
            $this->showModal = true;
        }
    }

    public function tutupModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->menuId = null;
        $this->nama = '';
        $this->kategori_id = '';
        $this->harga = '';
        $this->deskripsi = '';
        $this->gambar = null;
        $this->gambarLama = null;
    }

    public function simpanMenu()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|integer',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $this->nama,
            'kategori_id' => $this->kategori_id,
            'harga' => $this->harga,
            'deskripsi' => $this->deskripsi,
            'updated_at' => now(),
        ];

        if ($this->gambar) {
            $data['gambar'] = $this->gambar->store('menus', 'public');
            if ($this->isEditMode && $this->gambarLama) {
                Storage::disk('public')->delete($this->gambarLama);
            }
        } elseif ($this->isEditMode) {
            $data['gambar'] = $this->gambarLama;
        }

        if ($this->isEditMode) {
            DB::table('menus')->where('id', $this->menuId)->update($data);
            session()->flash('message', 'Menu berhasil diperbarui.');
        } else {
            $data['tersedia'] = 1;
            $data['created_at'] = now();
            DB::table('menus')->insert($data);
            session()->flash('message', 'Menu baru berhasil ditambahkan.');
        }

        $this->tutupModal();
    }

    // ==========================================
    // LOGIKA KATEGORI
    // ==========================================
    public function bukaModalKategori()
    {
        $this->resetKategoriForm();
        $this->showKategoriModal = true;
    }

    public function tutupModalKategori()
    {
        $this->showKategoriModal = false;
        $this->resetKategoriForm();
    }

    private function resetKategoriForm()
    {
        $this->kategoriId = null;
        $this->namaKategori = '';
        $this->tipeKategori = 'minuman';
        $this->isEditKategoriMode = false;
    }

    public function simpanKategori()
    {
        $this->validate([
            'namaKategori' => 'required|string|max:255',
            'tipeKategori' => 'required|in:makanan,minuman',
        ], [
            'namaKategori.required' => 'Nama kategori wajib diisi.',
            'tipeKategori.required' => 'Tipe wajib dipilih.',
        ]);

        if ($this->isEditKategoriMode) {
            DB::table('kategori_menus')->where('id', $this->kategoriId)->update([
                'nama' => $this->namaKategori,
                'tipe' => $this->tipeKategori,
                'updated_at' => now()
            ]);
            session()->flash('kategori_message', 'Kategori berhasil diperbarui.');
        } else {
            DB::table('kategori_menus')->insert([
                'nama' => $this->namaKategori,
                'tipe' => $this->tipeKategori,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            session()->flash('kategori_message', 'Kategori baru berhasil ditambahkan.');
        }

        $this->resetKategoriForm(); 
        // Biarkan modal tetap terbuka agar admin melihat hasilnya
    }

    public function editKategori($id)
    {
        $kat = DB::table('kategori_menus')->where('id', $id)->first();
        if ($kat) {
            $this->kategoriId = $kat->id;
            $this->namaKategori = $kat->nama;
            $this->tipeKategori = $kat->tipe ?? 'minuman';
            $this->isEditKategoriMode = true;
        }
    }

    public function hapusKategori($id)
    {
        // Proteksi: Cek apakah kategori masih dipakai oleh menu
        $dipakai = DB::table('menus')->where('kategori_id', $id)->exists();
        if ($dipakai) {
            session()->flash('kategori_error', 'Gagal: Kategori masih digunakan oleh produk.');
            return;
        }

        DB::table('kategori_menus')->where('id', $id)->delete();
        session()->flash('kategori_message', 'Kategori berhasil dihapus.');
    }

    // ==========================================
    // RENDER UTAMA
    // ==========================================
    public function render()
    {
        $kategoris = DB::table('kategori_menus')->get();
        $query = DB::table('menus')
            ->leftJoin('kategori_menus', 'menus.kategori_id', '=', 'kategori_menus.id')
            ->select('menus.*', 'kategori_menus.nama as nama_kategori');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('menus.nama', 'like', '%' . $this->search . '%')
                  ->orWhere('kategori_menus.nama', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->kategoriFilter !== 'semua') {
            $query->where('menus.kategori_id', $this->kategoriFilter);
        }

        $menus = $query->orderBy('menus.created_at', 'desc')->paginate(6);

        return view('livewire.menu-management', [
            'kategoris' => $kategoris,
            'menus' => $menus,
        ]);
    }
}