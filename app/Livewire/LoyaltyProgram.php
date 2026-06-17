<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoyaltyProgram extends Component
{
    use WithPagination;
    use WithFileUploads;

    // --- VARIABEL REWARD ---
    public $showModal = false;
    public $isEditMode = false;
    public $rewardId = null;
    public $nama, $deskripsi, $poin_dibutuhkan, $stok, $status = 'active';
    public $gambar, $gambarLama;

    // --- VARIABEL ATURAN POIN ---
    public $showRulesModal = false;
    public $nominalTransaksi = 10000;
    public $poinDidapat = 1;

    // ==========================================
    // LOGIKA ATURAN KONVERSI POIN
    // ==========================================
    public function bukaModalRules()
    {
        $aturan = DB::table('aturan_poins')->first();
        if ($aturan) {
            // Asumsi nama kolom di tabel kamu adalah nominal_transaksi & poin_didapat
            $this->nominalTransaksi = $aturan->nominal_transaksi ?? 10000;
            $this->poinDidapat = $aturan->poin_didapat ?? 1;
        }
        $this->showRulesModal = true;
    }

    public function tutupModalRules()
    {
        $this->showRulesModal = false;
    }

    public function simpanRules()
    {
        $this->validate([
            'nominalTransaksi' => 'required|numeric|min:1000',
            'poinDidapat' => 'required|numeric|min:1',
        ]);

        $aturan = DB::table('aturan_poins')->first();
        
        if ($aturan) {
            DB::table('aturan_poins')->where('id', $aturan->id)->update([
                'nominal_transaksi' => $this->nominalTransaksi,
                'poin_didapat' => $this->poinDidapat,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('aturan_poins')->insert([
                'nominal_transaksi' => $this->nominalTransaksi,
                'poin_didapat' => $this->poinDidapat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        session()->flash('message', 'Aturan konversi poin berhasil diperbarui!');
        $this->tutupModalRules();
    }

    // ==========================================
    // LOGIKA ITEM REWARD
    // ==========================================
    public function bukaModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function editReward($id)
    {
        $reward = DB::table('rewards')->where('id', $id)->first();
        if ($reward) {
            $this->rewardId = $reward->id;
            $this->nama = $reward->nama;
            $this->deskripsi = $reward->deskripsi;
            $this->poin_dibutuhkan = $reward->poin_dibutuhkan;
            $this->stok = $reward->stok;
            $this->status = $reward->status;
            $this->gambarLama = $reward->gambar;

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
        $this->rewardId = null;
        $this->nama = '';
        $this->deskripsi = '';
        $this->poin_dibutuhkan = '';
        $this->stok = '';
        $this->status = 'active';
        $this->gambar = null;
        $this->gambarLama = null;
    }

    public function simpanReward()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'poin_dibutuhkan' => 'required|integer|min:1',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'poin_dibutuhkan' => $this->poin_dibutuhkan,
            'stok' => $this->stok,
            'status' => $this->status,
            'updated_at' => now(),
        ];

        if ($this->gambar) {
            $data['gambar'] = $this->gambar->store('rewards', 'public');
            if ($this->isEditMode && $this->gambarLama) Storage::disk('public')->delete($this->gambarLama);
        } elseif ($this->isEditMode) {
            $data['gambar'] = $this->gambarLama;
        }

        if ($this->isEditMode) {
            DB::table('rewards')->where('id', $this->rewardId)->update($data);
            session()->flash('message', 'Item reward berhasil diperbarui.');
        } else {
            $data['created_at'] = now();
            DB::table('rewards')->insert($data);
            session()->flash('message', 'Item reward baru berhasil ditambahkan.');
        }

        $this->tutupModal();
    }

    public function hapusReward($id)
    {
        $reward = DB::table('rewards')->where('id', $id)->first();
        if ($reward && $reward->gambar) Storage::disk('public')->delete($reward->gambar);
        
        DB::table('rewards')->where('id', $id)->delete();
        session()->flash('message', 'Item reward dihapus.');
    }

    // ==========================================
    // RENDER HALAMAN
    // ==========================================
    public function render()
    {
        // 1. Ambil Aturan Poin Saat Ini
        $aturanPoin = DB::table('aturan_poins')->first();

        // 2. Ambil Daftar Katalog Reward
        $rewards = DB::table('rewards')->orderBy('created_at', 'desc')->get();
        
        // 3. Simulasi Total Poin Beredar
        $totalPoints = DB::table('users')->sum('poin') ?? 2485100;
        
        // 4. Ambil Riwayat Penukaran
        $redemptions = DB::table('penukaran_poins')
            ->leftJoin('users', 'penukaran_poins.user_id', '=', 'users.id')
            ->select('penukaran_poins.*', 'users.name as user_name', 'users.email')
            ->orderBy('penukaran_poins.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.loyalty-program', compact('rewards', 'totalPoints', 'redemptions', 'aturanPoin'));
    }
}