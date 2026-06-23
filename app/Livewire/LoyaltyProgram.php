<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoyaltyProgram extends Component
{
    use WithPagination;

    // Variabel Aturan Poin
    public $nominalTransaksi, $poinDidapat;
    public $showModalRules = false;

    // Variabel Katalog Voucher
    public $showModalVoucher = false;
    public $isEditMode = false;
    public $voucherId = null;

    // Field Form Katalog Voucher
    public $judul, $deskripsi, $poin_dibutuhkan;
    public $tipe_diskon = 'nominal'; // nominal / persen
    public $nilai_diskon, $status = 'aktif';
    public $berlaku_hingga;
    public $pajakPersen;

    public function mount()
    {
        // Ambil data aturan poin saat halaman dimuat
        $aturan = DB::table('aturan_poins')->first();
        
        // Ambil data dari tabel pengaturans secara mandiri
        $pengaturan = DB::table('pengaturans')->first();
        $this->pajakPersen = $pengaturan ? $pengaturan->pajak_persen : 11;

        if ($aturan) {
            $this->nominalTransaksi = $aturan->point_per_rupiah;
            $this->poinDidapat = $aturan->point_per_voucher;
        } else {
            $this->nominalTransaksi = 10000;
            $this->poinDidapat = 1;
        }
    }

    // --- FUNGSI ATURAN POIN ---
    public function bukaModalRules()
    {
        $this->showModalRules = true;
    }
    public function tutupModalRules()
    {
        $this->showModalRules = false;
    }

    public function simpanRules()
    {
        $this->validate([
            'nominalTransaksi' => 'required|numeric|min:1000',
            'poinDidapat' => 'required|numeric|min:1',
            'pajakPersen' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $aturan = DB::table('aturan_poins')->first();

            // 1. Simpan nilai pajak ke tabel khusus pengaturans
            DB::table('pengaturans')->updateOrInsert(
                ['id' => 1], 
                [
                    'pajak_persen' => $this->pajakPersen,
                    'updated_at' => now()
                ]
            );

            // 2. Siapkan data murni aturan poin untuk tabel aturan_poins (tanpa kolom pajak_persen)
            $dataToSave = [
                'nama' => 'Aturan Poin Vivalavida',
                'point_per_rupiah' => $this->nominalTransaksi,
                'point_per_voucher' => $this->poinDidapat,
                'tipe_diskon' => 'nominal', 
                'nilai_diskon' => 0,        
                'aktif' => 1,
                'updated_at' => now(),
            ];

            if ($aturan) {
                DB::table('aturan_poins')->where('id', $aturan->id)->update($dataToSave);
            } else {
                $dataToSave['created_at'] = now();
                DB::table('aturan_poins')->insert($dataToSave);
            }

            session()->flash('message', 'Aturan konversi poin dan sistem berhasil diperbarui!');
            $this->tutupModalRules();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan aturan: ' . $e->getMessage());
        }
    }

    // --- FUNGSI KATALOG VOUCHER ---
    public function bukaModalVoucher()
    {
        $this->resetFormVoucher();
        $this->isEditMode = false;
        $this->showModalVoucher = true;
    }

    public function tutupModalVoucher()
    {
        $this->showModalVoucher = false;
        $this->resetFormVoucher();
    }

    private function resetFormVoucher()
    {
        $this->voucherId = null;
        $this->judul = '';
        $this->deskripsi = '';
        $this->poin_dibutuhkan = '';
        $this->tipe_diskon = 'nominal';
        $this->nilai_diskon = '';
        $this->status = 'aktif';
        $this->berlaku_hingga = Carbon::now()->addDays(30)->format('Y-m-d');
    }

    public function editVoucher($id)
    {
        $voucher = DB::table('katalog_vouchers')->where('id', $id)->first();
        if ($voucher) {
            $this->voucherId = $voucher->id;
            $this->judul = $voucher->judul;
            $this->deskripsi = $voucher->deskripsi;
            $this->poin_dibutuhkan = $voucher->poin_dibutuhkan;
            $this->tipe_diskon = $voucher->tipe_diskon;
            $this->nilai_diskon = $voucher->nilai_diskon;
            $this->status = $voucher->status;
            $this->berlaku_hingga = Carbon::parse($voucher->berlaku_hingga)->format('Y-m-d');

            $this->isEditMode = true;
            $this->showModalVoucher = true;
        }
    }

    public function simpanVoucher()
    {
        $this->validate([
            'judul' => 'required|string|max:255',
            'poin_dibutuhkan' => 'required|integer|min:1',
            'tipe_diskon' => 'required|in:nominal,persen',
            'nilai_diskon' => 'required|numeric|min:1',
            'berlaku_hingga' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            $data = [
                'judul' => $this->judul,
                'deskripsi' => $this->deskripsi,
                'poin_dibutuhkan' => $this->poin_dibutuhkan,
                'tipe_diskon' => $this->tipe_diskon,
                'nilai_diskon' => $this->nilai_diskon,
                'berlaku_hingga' => $this->berlaku_hingga,
                'status' => $this->status,
                'updated_at' => now(),
            ];

            if ($this->isEditMode) {
                DB::table('katalog_vouchers')->where('id', $this->voucherId)->update($data);
                session()->flash('message', 'Katalog voucher berhasil diperbarui.');
            } else {
                $data['created_at'] = now();
                DB::table('katalog_vouchers')->insert($data);
                session()->flash('message', 'Voucher baru ditambahkan ke katalog.');
            }

            $this->tutupModalVoucher();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan voucher: ' . $e->getMessage());
        }
    }

    public function hapusVoucher($id)
    {
        try {
            DB::table('katalog_vouchers')->where('id', $id)->delete();
            session()->flash('message', 'Voucher berhasil dihapus dari katalog.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus voucher.');
        }
    }

    public function render()
    {
        $vouchers = DB::table('katalog_vouchers')->orderBy('created_at', 'desc')->paginate(8);
        $totalBeredar = DB::table('users')->sum('poin');
        $katalogAktif = DB::table('katalog_vouchers')->where('status', 'aktif')->count();

        return view('livewire.loyalty-program', compact('vouchers', 'totalBeredar', 'katalogAktif'));
    }
}