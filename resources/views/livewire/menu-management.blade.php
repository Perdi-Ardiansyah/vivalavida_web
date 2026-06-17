<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    <style>
        /* Menyembunyikan scrollbar agar tampilan tetap clean tapi tetap bisa di-scroll */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <x-admin-sidebar active="menu-management" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">

        @if (session()->has('message'))
            <div class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-circle-check"></i>
                <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Menu Management</h1>
                <p class="text-gray-500 text-sm">Manage your coffee beans, brews, and delicacies.</p>
            </div>
            <div>
                <button wire:click="bukaModal"
                    class="flex items-center px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                    <i class="fa-solid fa-circle-plus mr-2"></i> Tambah Produk Baru
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 mb-8">
            <div class="relative w-full lg:max-w-xs xl:max-w-sm flex-shrink-0">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-full py-3 pl-11 pr-4 focus:outline-none focus:border-[#046A41] shadow-sm bg-white" placeholder="Cari produk atau kategori...">
            </div>
            
            <div class="flex-1 flex bg-white p-1.5 rounded-full border border-gray-100 shadow-sm items-center overflow-hidden">
                
                <div class="flex-1 flex overflow-x-auto gap-2 hide-scrollbar pr-2">
                    <button wire:click="$set('kategoriFilter', 'semua')" class="flex-shrink-0 whitespace-nowrap px-5 py-1.5 rounded-full text-sm font-bold transition-all {{ $kategoriFilter === 'semua' ? 'bg-[#046A41] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                        Semua
                    </button>
                    
                    @foreach($kategoris as $kat)
                        <button wire:click="$set('kategoriFilter', '{{ $kat->id }}')" class="flex-shrink-0 whitespace-nowrap px-5 py-1.5 rounded-full text-sm font-bold transition-all {{ $kategoriFilter == $kat->id ? 'bg-[#046A41] text-white' : 'text-gray-500 hover:bg-gray-50' }}">
                            {{ $kat->nama }}
                        </button>
                    @endforeach
                </div>
                
                <div class="flex-shrink-0 flex items-center bg-white pl-2">
                    <div class="w-px h-6 bg-gray-200 mr-3"></div>
                    <button wire:click="bukaModalKategori" class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-bold text-[#046A41] hover:bg-green-50 transition border border-dashed border-[#046A41] flex items-center bg-white">
                        <i class="fa-solid fa-gear mr-1.5"></i> Kelola
                    </button>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
            @forelse($menus as $menu)
                @php
                    $isSoldOut = $menu->tersedia == 0;
                @endphp

                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden hover:shadow-md transition">
                    <div class="h-48 bg-gray-100 relative group">
                        @if($menu->gambar)
                            <img src="{{ asset('storage/' . $menu->gambar) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i
                                    class="fa-solid fa-image text-3xl"></i></div>
                        @endif

                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div
                            class="absolute top-3 left-3 bg-[#046A41]/90 backdrop-blur-sm text-white text-[9px] font-black uppercase px-2.5 py-1 rounded-md">
                            {{ $menu->nama_kategori }}</div>

                        @if($isSoldOut)
                            <div
                                class="absolute top-3 right-3 bg-red-600/90 backdrop-blur-sm text-white text-[9px] font-black uppercase px-2.5 py-1 rounded-md">
                                SOLD OUT</div>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1">{{ $menu->nama }}</h3>
                        <p class="text-sm font-black text-[#046A41] mb-3">Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </p>
                        <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed mb-4 flex-1">
                            {{ $menu->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                        <div class="pt-4 border-t border-gray-100 flex justify-between items-center mt-auto">
                            <button wire:click="toggleStatus({{ $menu->id }})"
                                class="flex items-center text-[10px] font-bold uppercase tracking-wider {{ $isSoldOut ? 'text-gray-400 hover:text-gray-600' : 'text-[#046A41] hover:text-emerald-800' }} transition">
                                <span
                                    class="w-2 h-2 rounded-full {{ $isSoldOut ? 'bg-gray-300' : 'bg-[#046A41]' }} mr-1.5"></span>
                                {{ $isSoldOut ? 'Nonaktif' : 'Aktif' }}
                            </button>

                            <div class="flex gap-3">
                                <button wire:click="editMenu({{ $menu->id }})"
                                    class="text-gray-400 hover:text-blue-600 transition p-1">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:click="hapusMenu({{ $menu->id }})"
                                    wire:confirm="Yakin ingin menghapus menu ini?"
                                    class="text-gray-400 hover:text-red-600 transition p-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-16 text-center shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900">Tidak ada produk ditemukan</h3>
                </div>
            @endforelse
        </div>

        <div class="flex justify-between items-center text-sm text-gray-500 font-medium">
            <div>Menampilkan {{ $menus->firstItem() ?? 0 }} sampai {{ $menus->lastItem() ?? 0 }} dari
                {{ $menus->total() }} produk</div>
            <div>{{ $menus->links() }}</div>
        </div>

        @if($showModal)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden max-h-[90vh] overflow-y-auto">
                    <div
                        class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
                        <h2 class="text-lg font-bold text-[#046A41]">
                            {{ $isEditMode ? 'Edit Data Produk' : 'Tambah Produk Baru' }}
                        </h2>
                        <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600"><i
                                class="fa-solid fa-xmark text-xl"></i></button>
                    </div>

                    <form wire:submit.prevent="simpanMenu" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Nama Produk</label>
                            <input wire:model="nama" type="text"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]"
                                placeholder="Cth: Iced Spanish Latte">
                            @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Kategori</label>
                            <select wire:model="kategori_id"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41] bg-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Harga (Rp)</label>
                            <input wire:model="harga" type="number"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]"
                                placeholder="Cth: 25000">
                            @error('harga') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi Singkat</label>
                            <textarea wire:model="deskripsi" rows="2"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-[#046A41]"
                                placeholder="Jelaskan detail racikan produk ini..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Gambar Produk</label>
                            <input wire:model="gambar" type="file" accept="image/*"
                                class="w-full border border-gray-200 rounded-lg px-4 py-1.5 text-sm focus:outline-none focus:border-[#046A41] file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-green-50 file:text-[#046A41] hover:file:bg-green-100">
                            <div wire:loading wire:target="gambar" class="text-xs text-[#046A41] font-bold mt-1">
                                Mengunggah...</div>
                            @error('gambar') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                            @if ($gambar)
                                <div class="mt-3 w-20 h-20 rounded-xl overflow-hidden border border-gray-200 relative">
                                    <img src="{{ $gambar->temporaryUrl() }}" class="w-full h-full object-cover">
                                </div>
                            @elseif ($isEditMode && $gambarLama)
                                <div class="mt-3 w-20 h-20 rounded-xl overflow-hidden border border-gray-200 relative">
                                    <img src="{{ asset('storage/' . $gambarLama) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="button" wire:click="tutupModal"
                                class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                            <button type="submit"
                                class="flex-1 py-2.5 bg-[#046A41] text-white font-bold rounded-xl hover:bg-emerald-800 transition text-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($showKategoriModal)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[85vh]">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h2 class="text-lg font-bold text-[#046A41]">Kelola Kategori Menu</h2>
                        <button wire:click="tutupModalKategori" class="text-gray-400 hover:text-gray-600"><i
                                class="fa-solid fa-xmark text-xl"></i></button>
                    </div>

                    @if (session()->has('kategori_message'))
                        <div class="bg-green-50 text-[#046A41] px-4 py-2 text-xs font-bold border-b border-green-100">
                            {{ session('kategori_message') }}</div>
                    @endif
                    @if (session()->has('kategori_error'))
                        <div class="bg-red-50 text-red-600 px-4 py-2 text-xs font-bold border-b border-red-100">
                            {{ session('kategori_error') }}</div>
                    @endif

                    <form wire:submit.prevent="simpanKategori" class="p-6 border-b border-gray-100 bg-white">
                        <div class="flex gap-3 items-start">
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nama
                                    Kategori</label>
                                <input wire:model="namaKategori" type="text"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#046A41]"
                                    placeholder="Cth: Pastry">
                                @error('namaKategori') <span
                                class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-1/3">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tipe</label>
                                <select wire:model="tipeKategori"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#046A41] bg-white">
                                    <option value="minuman">Minuman</option>
                                    <option value="makanan">Makanan</option>
                                </select>
                                @error('tipeKategori') <span
                                class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="pt-5">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-[#046A41] text-white font-bold rounded-lg hover:bg-emerald-800 transition text-sm h-full">
                                    {{ $isEditKategoriMode ? 'Update' : 'Tambah' }}
                                </button>
                            </div>
                        </div>
                        @if($isEditKategoriMode)
                            <button type="button" wire:click="bukaModalKategori"
                                class="text-xs text-blue-600 hover:underline mt-2 font-medium">Batal Edit</button>
                        @endif
                    </form>

                    <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Daftar Kategori</h3>
                        <div class="space-y-2">
                            @foreach($kategoris as $kat)
                                <div
                                    class="bg-white border border-gray-100 p-3 rounded-xl shadow-sm flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $kat->nama }}</h4>
                                        <span
                                            class="text-[10px] uppercase font-bold text-gray-400">{{ $kat->tipe ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="editKategori({{ $kat->id }})"
                                            class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <button wire:click="hapusKategori({{ $kat->id }})"
                                            wire:confirm="Hapus kategori {{ $kat->nama }}?"
                                            class="w-8 h-8 rounded-full bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </main>
</div>