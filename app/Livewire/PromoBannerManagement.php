<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PromoBannerManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';

    // Variabel Form Modal
    public $showModal = false;
    public $isEditMode = false;
    public $promoId = null;

    // Field Form sesuai kebutuhan Flutter (judul, deskripsi, gambar, tag)
    public $judul, $deskripsi, $tag, $status = 'active';
    public $gambar, $gambarLama;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function bukaModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function editPromo($id)
    {
        $promo = DB::table('promos')->where('id', $id)->first();
        if ($promo) {
            $this->promoId = $promo->id;
            $this->judul = $promo->judul;
            $this->deskripsi = $promo->deskripsi;
            $this->tag = $promo->tag ?? '';
            $this->status = $promo->status ?? 'active';
            $this->gambarLama = $promo->gambar;

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
        $this->promoId = null;
        $this->judul = '';
        $this->deskripsi = '';
        $this->tag = '';
        $this->status = 'active';
        $this->gambar = null;
        $this->gambarLama = null;
    }

    public function simpanPromo()
    {
        $this->validate([
            'judul' => 'string|max:255',
            'gambar' => 'nullable|image|max:2048', // Maksimal 2MB
        ]);

        try {
            $data = [
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'tag' => $this->tag,
                'updated_at' => now(),
            ];

            // Proses unggah gambar baru ke folder storage/app/public/promos
            if ($this->gambar) {
                $data['gambar'] = $this->gambar->store('promos', 'public');

                // Hapus gambar lama jika sedang edit
                if ($this->isEditMode && $this->gambarLama) {
                    Storage::disk('public')->delete($this->gambarLama);
                }
            } elseif ($this->isEditMode) {
                $data['gambar'] = $this->gambarLama;
            } else {
                // Jika buat baru tapi tidak upload gambar, tolak.
                if (!$this->gambar) {
                    session()->flash('error', 'Gambar banner wajib diunggah untuk promo baru.');
                    return;
                }
            }

            if ($this->isEditMode) {
                DB::table('promos')->where('id', $this->promoId)->update($data);
                session()->flash('message', 'Banner promo berhasil diperbarui.');
            } else {
                $data['created_at'] = now();
                DB::table('promos')->insert($data);
                session()->flash('message', 'Banner promo baru berhasil ditambahkan.');
            }

            $this->tutupModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan database: ' . $e->getMessage());
        }
    }

    public function hapusPromo($id)
    {
        $promo = DB::table('promos')->where('id', $id)->first();
        if ($promo && $promo->gambar) {
            Storage::disk('public')->delete($promo->gambar);
        }

        DB::table('promos')->where('id', $id)->delete();
        session()->flash('message', 'Banner promo berhasil dihapus.');
    }

    public function render()
    {
        $query = DB::table('promos');

        if (!empty($this->search)) {
            $query->where('judul', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
        }

        $promos = $query->orderBy('created_at', 'desc')->paginate(6);

        return view('livewire.promo-banner-management', compact('promos'));
    }
}