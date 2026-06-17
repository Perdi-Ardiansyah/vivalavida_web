<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="vouchers" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">
        
        @if (session()->has('message'))
            <div class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-circle-check"></i> <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Voucher & Promo Catalog</h1>
                <p class="text-gray-500 text-sm">Kelola daftar promo yang bisa diklaim pelanggan menggunakan poin.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-xl py-2.5 pl-9 pr-4 focus:outline-none focus:border-[#046A41] bg-white shadow-sm" placeholder="Cari katalog promo...">
                </div>
                <button wire:click="bukaModal" class="flex items-center px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition whitespace-nowrap">
                    <i class="fa-solid fa-circle-plus mr-2"></i> Buat Katalog Promo
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xl"><i class="fa-solid fa-gift"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Promo Aktif</p>
                    <h3 class="text-xl font-black text-gray-900">{{ $activePromos }}</h3>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl"><i class="fa-solid fa-ticket"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Diklaim</p>
                    <h3 class="text-xl font-black text-gray-900">{{ number_format($totalRedemptions) }}x</h3>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            <button wire:click="$set('filterStatus', 'all')" class="px-5 py-2 rounded-full text-sm font-bold transition-all {{ $filterStatus === 'all' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">Semua Promo</button>
            <button wire:click="$set('filterStatus', 'aktif')" class="px-5 py-2 rounded-full text-sm font-bold transition-all {{ $filterStatus === 'aktif' || $filterStatus === 'active' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">Aktif</button>
            <button wire:click="$set('filterStatus', 'expired')" class="px-5 py-2 rounded-full text-sm font-bold transition-all {{ $filterStatus === 'expired' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">Expired</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @forelse($promos as $promo)
                @php
                    $statusStr = strtolower($promo->status ?? 'aktif');
                    $isExpired = $statusStr === 'expired' || $statusStr === 'kadaluarsa';
                    $isActive = $statusStr === 'active' || $statusStr === 'aktif';
                    
                    $statusColor = $isActive ? 'text-teal-600 bg-teal-50' : ($isExpired ? 'text-gray-400 bg-gray-100' : 'text-orange-600 bg-orange-50');
                    $opacity = $isExpired ? 'opacity-60 grayscale-[50%]' : '';
                @endphp
                
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col relative {{ $opacity }} hover:shadow-md transition">
                    <div class="absolute top-6 right-6 px-2.5 py-1 rounded text-[9px] font-black uppercase tracking-widest {{ $statusColor }}">
                        {{ $promo->status ?? 'AKTIF' }}
                    </div>

                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-16 h-16 rounded-full bg-orange-50 border-2 border-orange-100 flex flex-col items-center justify-center flex-shrink-0 text-center">
                            @if(($promo->tipe_diskon ?? 'persen') === 'persen')
                                <span class="text-lg font-black text-orange-600 leading-none">{{ (int)$promo->nilai_diskon }}%</span>
                                <span class="text-[9px] font-bold text-orange-400 mt-0.5">OFF</span>
                            @else
                                <span class="text-[10px] font-bold text-orange-400 mb-0.5">Rp</span>
                                <span class="text-sm font-black text-orange-600 leading-none">{{ number_format($promo->nilai_diskon/1000, 0) }}k</span>
                            @endif
                        </div>
                        <div class="pr-12">
                            <h3 class="text-base font-bold text-gray-900 leading-tight mb-1">{{ $promo->judul ?? 'Promo Diskon' }}</h3>
                            <p class="text-[11px] font-bold text-[#046A41] uppercase tracking-wider mb-1 flex items-center">
                                <i class="fa-solid fa-coins mr-1.5"></i> Harga: {{ number_format($promo->poin_dibutuhkan ?? 0) }} Poin
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 line-clamp-2 mb-6 h-8">{{ $promo->deskripsi }}</p>

                    <div class="flex justify-between items-end mb-6 mt-auto">
                        <div>
                            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">BATAS KLAIM</span>
                            <span class="text-xs font-bold text-gray-900">
                                {{ $promo->berlaku_hingga ? \Carbon\Carbon::parse($promo->berlaku_hingga)->format('M d, Y') : 'Tanpa Batas Waktu' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="editPromo({{ $promo->id }})" class="flex-1 py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                            Edit Katalog
                        </button>
                        <button wire:click="hapusPromo({{ $promo->id }})" wire:confirm="Hapus katalog promo ini?" class="w-10 h-10 border border-gray-200 rounded-xl text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <i class="fa-solid fa-gift text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900">Belum ada katalog promo</h3>
                    <p class="text-sm text-gray-500 mt-1">Buat penawaran diskon yang bisa diklaim dengan poin pelanggan.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $promos->links() }}</div>

        @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-[#046A41]">
                        {{ $isEditMode ? 'Edit Katalog Promo' : 'Buat Promo Baru' }}
                    </h2>
                    <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="simpanPromo" class="p-6 space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Judul Promo</label>
                        <input wire:model="judul" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]" placeholder="Cth: Diskon Spesial Akhir Pekan">
                        @error('judul') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi & Syarat</label>
                        <textarea wire:model="deskripsi" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]" placeholder="Jelaskan detail promo..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-orange-600 mb-1"><i class="fa-solid fa-coins mr-1"></i> Harga Tukar Poin</label>
                            <input wire:model="poin_dibutuhkan" type="number" class="w-full border border-orange-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-orange-500 bg-orange-50/30" placeholder="Cth: 500">
                            @error('poin_dibutuhkan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Tipe Diskon</label>
                            <select wire:model.live="tipe_diskon" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41] bg-white">
                                <option value="persen">Persentase (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Nilai Potongan</label>
                            <input wire:model="nilai_diskon" type="number" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]">
                            @error('nilai_diskon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                            <select wire:model="status" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41] bg-white">
                                <option value="aktif">Aktif (Tersedia)</option>
                                <option value="expired">Expired (Tutup)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Batas Waktu Klaim</label>
                            <input wire:model="berlaku_hingga" type="date" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]">
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="tutupModal" class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 bg-[#046A41] text-white font-bold rounded-xl hover:bg-emerald-800 transition text-sm">
                            <i class="fa-solid fa-save mr-1.5"></i> Simpan Katalog
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </main>
</div>