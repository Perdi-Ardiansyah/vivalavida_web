<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class VoucherPromoManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all'; 

    // Variabel Modal
    public $showModal = false;
    public $isEditMode = false;
    public $katalogId = null;

    // Field untuk tabel katalog_vouchers
    public $judul, $deskripsi, $poin_dibutuhkan, $tipe_diskon = 'persen', $nilai_diskon, $status = 'aktif', $berlaku_hingga;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function bukaModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function editPromo($id)
    {
        $katalog = DB::table('katalog_vouchers')->where('id', $id)->first();
        if ($katalog) {
            $this->katalogId = $katalog->id;
            $this->judul = $katalog->judul;
            $this->deskripsi = $katalog->deskripsi;
            $this->poin_dibutuhkan = $katalog->poin_dibutuhkan;
            $this->tipe_diskon = $katalog->tipe_diskon;
            $this->nilai_diskon = $katalog->nilai_diskon;
            $this->status = $katalog->status;
            $this->berlaku_hingga = $katalog->berlaku_hingga;

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
        $this->katalogId = null;
        $this->judul = '';
        $this->deskripsi = '';
        $this->poin_dibutuhkan = '';
        $this->tipe_diskon = 'persen';
        $this->nilai_diskon = '';
        $this->status = 'aktif';
        $this->berlaku_hingga = '';
    }

    public function simpanPromo()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'poin_dibutuhkan' => 'required|integer|min:0',
            'tipe_diskon' => 'required|in:persen,nominal',
            'nilai_diskon' => 'required|numeric|min:1',
            'status' => 'required|string',
        ]);

        $data = [
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'poin_dibutuhkan' => $this->poin_dibutuhkan,
            'tipe_diskon' => $this->tipe_diskon,
            'nilai_diskon' => $this->nilai_diskon,
            'status' => $this->status,
            'berlaku_hingga' => $this->berlaku_hingga ?: null,
            'updated_at' => now(),
        ];

        if ($this->isEditMode) {
            DB::table('katalog_vouchers')->where('id', $this->katalogId)->update($data);
            session()->flash('message', 'Katalog promo berhasil diperbarui.');
        } else {
            $data['created_at'] = now();
            DB::table('katalog_vouchers')->insert($data);
            session()->flash('message', 'Katalog promo baru berhasil ditambahkan.');
        }

        $this->tutupModal();
    }

    public function hapusPromo($id)
    {
        DB::table('katalog_vouchers')->where('id', $id)->delete();
        session()->flash('message', 'Katalog promo berhasil dihapus.');
    }

    public function render()
    {
        // Hitung promo yang aktif di katalog
        $activePromos = DB::table('katalog_vouchers')->where('status', 'active')->orWhere('status', 'aktif')->count();
        // Total klaim bisa kita ambil dari voucher_diskons milik pelanggan
        $totalRedemptions = DB::table('voucher_diskons')->count(); 

        $query = DB::table('katalog_vouchers');

        if (!empty($this->search)) {
            $query->where('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $promos = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('livewire.voucher-promo-management', compact(
            'promos', 'activePromos', 'totalRedemptions'
        ));
    }
}