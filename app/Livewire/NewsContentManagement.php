<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NewsContentManagement extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $filterCategory = 'all'; 
    public $viewMode = 'list'; // Fitur toggle list/grid

    // Variabel Form Modal
    public $showModal = false;
    public $isEditMode = false;
    public $articleId = null;

    public $judul, $penulis, $kategori = 'Berita', $konten, $status = 'Publish';
    public $gambar, $gambarLama;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterCategory() { $this->resetPage(); }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function bukaModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
        // Set default penulis berdasarkan user yang sedang login (jika ada sistem auth)
        $this->penulis = auth()->user()->name ?? 'Admin';
    }

    public function editArticle($id)
    {
        $article = DB::table('articles')->where('id', $id)->first();
        if ($article) {
            $this->articleId = $article->id;
            $this->judul = $article->judul;
            $this->penulis = $article->penulis;
            $this->kategori = $article->kategori;
            $this->konten = $article->konten;
            $this->status = $article->status;
            $this->gambarLama = $article->gambar;

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
        $this->articleId = null;
        $this->judul = '';
        $this->penulis = '';
        $this->kategori = 'Berita';
        $this->konten = '';
        $this->status = 'Publish';
        $this->gambar = null;
        $this->gambarLama = null;
    }

    public function simpanArticle()
    {
        // Validasi input form
        $this->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:100',
            'kategori' => 'required|in:Berita,Promo,Event',
            'status' => 'required|in:Publish,Draft',
            'gambar' => 'nullable|image|max:2048', // Maksimal 2MB
        ], [
            'judul.required' => 'Judul konten wajib diisi.',
            'penulis.required' => 'Nama penulis wajib diisi.',
            'gambar.image' => 'Berkas harus berupa gambar (png, jpg, jpeg).',
            'gambar.max' => 'Ukuran gambar maksimal adalah 2MB.'
        ]);

        try {
            $data = [
                'judul' => $this->judul,
                'penulis' => $this->penulis,
                'kategori' => $this->kategori,
                'konten' => $this->konten,
                'status' => $this->status,
                'updated_at' => now(),
            ];

            // Proses unggah gambar baru jika ada
            if ($this->gambar) {
                $data['gambar'] = $this->gambar->store('articles', 'public');
                
                // Hapus gambar fisik lama jika dalam mode edit
                if ($this->isEditMode && $this->gambarLama) {
                    Storage::disk('public')->delete($this->gambarLama);
                }
            } elseif ($this->isEditMode) {
                $data['gambar'] = $this->gambarLama;
            }

            if ($this->isEditMode) {
                // Update data artikel
                DB::table('articles')->where('id', $this->articleId)->update($data);
                session()->flash('message', 'Artikel berhasil diperbarui.');
            } else {
                // Tambah data artikel baru
                $data['created_at'] = now();
                $data['views'] = 0;
                DB::table('articles')->insert($data);
                session()->flash('message', 'Artikel baru berhasil diterbitkan.');
            }

            $this->tutupModal();

        } catch (\Exception $e) {
            // Menangkap jika ada kolom database yang tidak cocok/salah nama
            session()->flash('message', 'ERROR DATABASE: ' . $e->getMessage());
        }
    }

    public function hapusArticle($id)
    {
        $article = DB::table('articles')->where('id', $id)->first();
        if ($article && $article->gambar) {
            Storage::disk('public')->delete($article->gambar);
        }
        
        DB::table('articles')->where('id', $id)->delete();
        session()->flash('message', 'Artikel berhasil dihapus.');
    }

    public function render()
    {
        // Statistik Dinamis
        $totalArticles = DB::table('articles')->count();
        $totalViews = DB::table('articles')->sum('views');
        
        // Mockup data statis untuk Interaksi & Share karena butuh tabel analitik kompleks
        $interactions = 1240; 
        $sharedCount = 892;

        $query = DB::table('articles');

        if (!empty($this->search)) {
            $query->where('judul', 'like', '%' . $this->search . '%')
                  ->orWhere('penulis', 'like', '%' . $this->search . '%');
        }

        if ($this->filterCategory !== 'all') {
            $query->where('kategori', $this->filterCategory);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.news-content-management', compact(
            'articles', 'totalArticles', 'totalViews', 'interactions', 'sharedCount'
        ));
    }
}