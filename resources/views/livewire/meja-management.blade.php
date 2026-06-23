<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="mejas" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">
        
        @if (session()->has('message'))
            <div class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-circle-check"></i> <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="absolute top-8 right-10 bg-red-50 border border-red-500 text-red-600 px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <i class="fa-solid fa-circle-exclamation"></i> <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Manajemen Meja & Dine-In</h1>
                <p class="text-gray-500 text-sm">Atur ketersediaan dan kapasitas meja untuk pesanan makan di tempat.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-lg py-2.5 pl-9 pr-4 focus:outline-none focus:border-[#046A41] bg-white shadow-sm" placeholder="Cari nomor meja...">
                </div>
                <button wire:click="bukaModal" class="flex items-center px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                    <i class="fa-solid fa-plus mr-2"></i> Tambah Meja
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 mr-4"><i class="fa-solid fa-chair text-xl"></i></div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Meja</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalMeja }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-[#046A41] mr-4"><i class="fa-solid fa-check-double text-xl"></i></div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tersedia (Kosong)</p>
                    <p class="text-2xl font-bold text-[#046A41]">{{ $mejaTersedia }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 mr-4"><i class="fa-solid fa-users text-xl"></i></div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Sedang Terisi</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $mejaTerisi }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-6 mb-8">
            @forelse($mejas as $meja)
                <div class="bg-white rounded-2xl p-5 border shadow-sm relative group overflow-hidden transition hover:shadow-md 
                    {{ $meja->status == 'tersedia' ? 'border-green-100' : ($meja->status == 'terisi' ? 'border-orange-200 bg-orange-50/30' : 'border-gray-200 bg-gray-50') }}">
                    
                    <div class="absolute top-3 right-3 w-3 h-3 rounded-full shadow-sm
                        {{ $meja->status == 'tersedia' ? 'bg-green-500' : ($meja->status == 'terisi' ? 'bg-orange-500' : 'bg-gray-400') }}">
                    </div>

                    <div class="text-center mt-2">
                        <i class="fa-solid fa-table-cells-large text-3xl mb-3 
                            {{ $meja->status == 'tersedia' ? 'text-green-600' : ($meja->status == 'terisi' ? 'text-orange-500' : 'text-gray-400') }}"></i>
                        <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $meja->nomor }}</h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium"><i class="fa-solid fa-user-group mr-1"></i> {{ $meja->kapasitas }} Kursi</p>
                    </div>
                    
                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm">
                        <button wire:click="tampilQr({{ $meja->id }})" class="w-9 h-9 rounded-full bg-white text-[#046A41] flex items-center justify-center hover:scale-110 transition shadow-lg" title="Lihat QR Code">
                            <i class="fa-solid fa-qrcode"></i>
                        </button>
                        <button wire:click="editMeja({{ $meja->id }})" class="w-9 h-9 rounded-full bg-white text-blue-600 flex items-center justify-center hover:scale-110 transition shadow-lg" title="Edit Meja">
                            <i class="fa-solid fa-pen text-sm"></i>
                        </button>
                        <button wire:click="hapusMeja({{ $meja->id }})" wire:confirm="Hapus meja {{ $meja->nomor }} secara permanen?" class="w-9 h-9 rounded-full bg-white text-red-500 flex items-center justify-center hover:scale-110 transition shadow-lg" title="Hapus Meja">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center bg-white rounded-2xl border border-gray-100 border-dashed">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4"><i class="fa-solid fa-chair text-2xl"></i></div>
                    <h3 class="text-lg font-bold text-gray-900">Belum Ada Meja</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-4">Tambahkan data meja untuk fasilitas Dine-In pelanggan.</p>
                    <button wire:click="bukaModal" class="px-5 py-2 text-[#046A41] font-bold bg-green-50 rounded-lg hover:bg-green-100 transition text-sm">Tambah Meja Pertama</button>
                </div>
            @endforelse
        </div>
        
        <div class="mb-8">{{ $mejas->links() }}</div>

        @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-[#046A41]">{{ $isEditMode ? 'Edit Data Meja' : 'Tambah Meja Baru' }}</h2>
                    <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="simpanMeja" class="flex flex-col flex-1 overflow-hidden">
                    <div class="p-6 overflow-y-auto space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Nomor / Nama Meja</label>
                            <input wire:model="nomor" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Cth: Meja 01, VIP A">
                            @error('nomor') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Kapasitas Kursi</label>
                                <input wire:model="kapasitas" type="number" min="1" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Cth: 2">
                                @error('kapasitas') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Status Ketersediaan</label>
                                <select wire:model="status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] bg-white focus:outline-none font-medium">
                                    <option value="tersedia">🟢 Tersedia</option>
                                    <option value="terisi">🟠 Sedang Terisi</option>
                                    <option value="nonaktif">⚫ Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex gap-3 justify-end mt-auto">
                        <button type="button" wire:click="tutupModal" class="px-5 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white font-bold rounded-xl text-sm transition">Simpan Meja</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($showQrModal && $qrMejaData)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
            <div id="print-area" class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col text-center relative p-8 border-4 border-[#046A41]">
                
                <button wire:click="tutupQrModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition print:hidden"><i class="fa-solid fa-xmark text-xl"></i></button>

                <div class="mb-4 text-[#046A41] flex justify-center">
                    <i class="fa-solid fa-mug-hot text-3xl"></i>
                </div>
                
                <h2 class="text-xl font-black text-gray-900 tracking-wide uppercase">Vivalavida Coffee</h2>
                <div class="w-12 h-1 bg-[#046A41] mx-auto mt-2 mb-6 rounded-full"></div>

                <div class="bg-white p-2 rounded-2xl inline-block mx-auto mb-6 shadow-sm border border-gray-100">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=VLV-MEJA-{{ $qrMejaData->id }}" alt="QR Meja {{ $qrMejaData->nomor }}" class="w-56 h-56 object-contain">
                </div>

                <div class="bg-[#046A41] text-white py-2 rounded-xl mb-4">
                    <p class="text-xs uppercase tracking-widest font-medium opacity-80 mb-0.5">NOMOR MEJA</p>
                    <p class="text-3xl font-black">{{ $qrMejaData->nomor }}</p>
                </div>

                <p class="text-xs text-gray-500 font-medium leading-relaxed print:hidden">Scan QR ini melalui aplikasi Vivalavida untuk langsung memesan dari meja Anda.</p>

                <div class="mt-6 print:hidden">
                    <button onclick="window.print()" class="w-full py-3 bg-[#046A41] hover:bg-emerald-800 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center">
                        <i class="fa-solid fa-print mr-2"></i> Cetak QR Code Meja
                    </button>
                </div>
            </div>
        </div>

        <style>
            @media print {
                body * { visibility: hidden; }
                #print-area, #print-area * { visibility: visible; }
                #print-area { position: absolute; left: 0; top: 0; width: 100%; height: auto; box-shadow: none; border: none; }
            }
        </style>
        @endif

    </main>
</div>