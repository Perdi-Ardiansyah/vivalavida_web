<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CustomerManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $orderRange = 'all';

    // --- VARIABEL BARU: MODAL DETAIL PELANGGAN ---
    public $showDetailModal = false;
    public $selectedCustomer = null;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingOrderRange() { $this->resetPage(); }

    // --- FUNGSI DETAIL PELANGGAN DIHIDUPKAN ---
    public function lihatDetail($id)
    {
        // Tarik data profil lengkap beserta akumulasi transaksinya
        $customerData = DB::table('users')
            ->select(
                'users.*', 
                DB::raw('(SELECT COUNT(*) FROM pesanans WHERE pesanans.user_id = users.id AND pesanans.status = "completed") as total_orders'),
                DB::raw('(SELECT SUM(total_akhir) FROM pesanans WHERE pesanans.user_id = users.id AND pesanans.status = "completed") as total_spent')
            )
            ->where('users.id', $id)
            ->first();

        if ($customerData) {
            $this->selectedCustomer = $customerData;
            
            // Tarik 5 transaksi terbaru dari pelanggan ini untuk ditampilkan di modal
            $this->selectedCustomer->riwayat_pesanans = DB::table('pesanans')
                ->where('user_id', $id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $this->showDetailModal = true;
        }
    }

    public function tutupDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedCustomer = null;
    }

    public function blokirPelanggan($id)
    {
        session()->flash('error_message', 'Akses pengguna #' . $id . ' telah dibatasi sementara waktu.');
    }

    public function render()
    {
        $query = DB::table('users')
            ->select(
                'users.id', 
                'users.name', 
                'users.email', 
                'users.role', // TUGAS 1: AMBIL KOLOM ROLE DARI DATABASE
                'users.created_at',
                DB::raw('(SELECT COUNT(*) FROM pesanans WHERE pesanans.user_id = users.id AND pesanans.status = "completed") as total_orders'),
                DB::raw('(SELECT SUM(total_akhir) FROM pesanans WHERE pesanans.user_id = users.id AND pesanans.status = "completed") as total_spent')
            );

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('users.name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->orderRange === 'high') {
            $query->having('total_orders', '>=', 50);
        } elseif ($this->orderRange === 'medium') {
            $query->havingBetween('total_orders', [10, 49]);
        } elseif ($this->orderRange === 'low') {
            $query->having('total_orders', '<', 10);
        }

        $customers = $query->orderBy('total_orders', 'desc')->paginate(10);

        return view('livewire.customer-management', compact('customers'));
    }
}