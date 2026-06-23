<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class MejaManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    // State Modal Form
    public $showModal = false;
    public $isEditMode = false;
    public $mejaId = null;

    // State Modal QR
    public $showQrModal = false;
    public $qrMejaData = null;

    // Field Form
    public $nomor;
    public $kapasitas = 2;
    public $status = 'tersedia';

    public function updatingSearch() { $this->resetPage(); }

    // --- FUNGSI MODAL FORM (CRUD) ---
    public function bukaModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function editMeja($id)
    {
        $meja = DB::table('mejas')->where('id', $id)->first();
        if ($meja) {
            $this->mejaId = $meja->id;
            $this->nomor = $meja->nomor;
            $this->kapasitas = $meja->kapasitas ?? 2;
            $this->status = $meja->status ?? 'tersedia';

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
        $this->mejaId = null;
        $this->nomor = '';
        $this->kapasitas = 2;
        $this->status = 'tersedia';
    }

    public function simpanMeja()
    {
        $this->validate([
            'nomor' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,terisi,nonaktif',
        ]);

        try {
            $data = [
                'nomor' => $this->nomor,
                'kapasitas' => $this->kapasitas,
                'status' => $this->status,
                'updated_at' => now(),
            ];

            if ($this->isEditMode) {
                $exists = DB::table('mejas')->where('nomor', $this->nomor)->where('id', '!=', $this->mejaId)->exists();
                if ($exists) {
                    session()->flash('error', 'Nomor meja ini sudah ada!');
                    return;
                }

                DB::table('mejas')->where('id', $this->mejaId)->update($data);
                session()->flash('message', 'Data meja berhasil diperbarui.');
            } else {
                $exists = DB::table('mejas')->where('nomor', $this->nomor)->exists();
                if ($exists) {
                    session()->flash('error', 'Nomor meja ini sudah ada!');
                    return;
                }

                $data['created_at'] = now();
                DB::table('mejas')->insert($data);
                session()->flash('message', 'Meja baru berhasil ditambahkan.');
            }

            $this->tutupModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function hapusMeja($id)
    {
        try {
            DB::table('mejas')->where('id', $id)->delete();
            session()->flash('message', 'Meja berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus meja. Pastikan meja ini tidak terkait dengan pesanan.');
        }
    }

    // --- FUNGSI MODAL QR CODE ---
    public function tampilQr($id)
    {
        $this->qrMejaData = DB::table('mejas')->where('id', $id)->first();
        if ($this->qrMejaData) {
            $this->showQrModal = true;
        }
    }

    public function tutupQrModal()
    {
        $this->showQrModal = false;
        $this->qrMejaData = null;
    }

    public function render()
    {
        $query = DB::table('mejas');

        if (!empty($this->search)) {
            $query->where('nomor', 'like', '%' . $this->search . '%');
        }

        $mejas = $query->orderBy('nomor', 'asc')->paginate(12);

        $totalMeja = DB::table('mejas')->count();
        $mejaTersedia = DB::table('mejas')->where('status', 'tersedia')->count();
        $mejaTerisi = DB::table('mejas')->where('status', 'terisi')->count();

        return view('livewire.meja-management', compact('mejas', 'totalMeja', 'mejaTersedia', 'mejaTerisi'));
    }
}