<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="promos" />

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
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Banner & Slider Management</h1>
                <p class="text-gray-500 text-sm">Kelola gambar promosi yang tampil bergeser di halaman utama aplikasi pelanggan.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-lg py-2.5 pl-9 pr-4 focus:outline-none focus:border-[#046A41] bg-white shadow-sm" placeholder="Cari banner...">
                </div>
                <button wire:click="bukaModal" class="flex items-center px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                    <i class="fa-solid fa-image mr-2"></i> Tambah Banner Baru
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @forelse($promos as $promo)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden hover:shadow-md transition">
                    <div class="h-48 bg-gray-200 relative overflow-hidden group">
                        @if($promo->gambar)
                            <img src="{{ asset('storage/' . $promo->gambar) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-image text-4xl"></i></div>
                        @endif
                        
                       

                        @if(!empty($promo->tag))
                        <div class="absolute bottom-4 left-4 px-2.5 py-1 rounded-md bg-orange-500 text-white text-[10px] font-black uppercase tracking-widest shadow-sm">
                            {{ $promo->tag }}
                        </div>
                        @endif
                    </div>

                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="text-base font-bold text-gray-900 leading-tight mb-2">{{ $promo->judul }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-4 flex-1">{{ $promo->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                        
                        <div class="flex gap-2 mt-auto pt-4 border-t border-gray-50">
                            <button wire:click="editPromo({{ $promo->id }})" class="flex-1 py-2 bg-gray-50 text-[#046A41] border border-gray-200 rounded-lg text-xs font-bold hover:bg-[#046A41] hover:text-white transition">
                                Edit Banner
                            </button>
                            <button wire:click="hapusPromo({{ $promo->id }})" wire:confirm="Yakin ingin menghapus banner ini dari aplikasi?" class="w-10 h-10 flex items-center justify-center text-gray-400 border border-gray-200 rounded-lg hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center bg-white rounded-2xl border border-gray-100 border-dashed">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4"><i class="fa-solid fa-images text-2xl"></i></div>
                    <h3 class="text-lg font-bold text-gray-900">Belum Ada Banner</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-4">Tambahkan gambar promo untuk menghidupkan beranda aplikasi pelanggan.</p>
                    <button wire:click="bukaModal" class="px-5 py-2 text-[#046A41] font-bold bg-green-50 rounded-lg hover:bg-green-100 transition text-sm">Upload Banner</button>
                </div>
            @endforelse
        </div>
        
        <div class="mb-8">{{ $promos->links() }}</div>

        @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-[#046A41]">{{ $isEditMode ? 'Edit Banner Slider' : 'Upload Banner Baru' }}</h2>
                    <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="simpanPromo" class="flex flex-col flex-1 overflow-hidden">
                    <div class="p-6 overflow-y-auto space-y-4">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Judul Utama</label>
                            <input wire:model="judul" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Cth: Diskon Kopi Susu Aren">
                            @error('judul') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi Singkat (Opsional)</label>
                            <textarea wire:model="deskripsi" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Cth: Nikmati promo spesial hari ini..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Label / Tag (Opsional)</label>
                                <input wire:model="tag" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Cth: PROMO">
                            </div>
                        </div>

                        <div class="mt-2 p-4 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50">
                            <label class="block text-xs font-bold text-gray-600 mb-2">Unggah File Gambar</label>
                            <input wire:model="gambar" type="file" accept="image/*" class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-bold file:bg-[#046A41] file:text-white hover:file:bg-emerald-800 transition cursor-pointer">
                            <p class="text-[10px] text-gray-400 mt-2">Disarankan rasio Landscape (16:9). Maks 2MB.</p>
                            
                            <div wire:loading wire:target="gambar" class="text-xs text-[#046A41] font-bold mt-2 flex items-center">
                                <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Sedang memproses gambar...
                            </div>
                            @error('gambar') <span class="text-red-500 text-xs mt-2 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex gap-3 justify-end mt-auto">
                        <button type="button" wire:click="tutupModal" class="px-5 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white font-bold rounded-xl text-sm transition">
                            Simpan Banner
                        </button>
                    </div>
                </form>

            </div>
        </div>
        @endif

    </main>
</div>