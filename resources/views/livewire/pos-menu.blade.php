<div class="flex h-screen overflow-hidden w-full bg-[#FAFAFA] font-sans antialiased text-gray-800 relative">
    
    <x-sidebar active="menu" />

    <main class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        
        <div class="flex-1 flex flex-col h-full overflow-y-auto p-6 lg:p-8">
            <div class="mb-6 space-y-4 border-b border-gray-100 pb-6">
                <div class="relative w-full max-w-2xl">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-xl py-3.5 pl-12 pr-4 focus:outline-none focus:border-[#046A41] bg-white shadow-sm" placeholder="Cari menu kopi atau makanan...">
                </div>

                <div class="flex flex-wrap gap-2">
                    <button wire:click="$set('kategoriFilter', 'semua')" class="px-5 py-2 rounded-full text-sm font-bold transition-all border {{ $kategoriFilter === 'semua' ? 'bg-[#046A41] text-white border-[#046A41]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        Semua
                    </button>
                    @foreach($kategoris as $kat)
                        <button wire:click="$set('kategoriFilter', '{{ $kat->id }}')" class="px-5 py-2 rounded-full text-sm font-bold transition-all border {{ $kategoriFilter == $kat->id ? 'bg-[#046A41] text-white border-[#046A41]' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                            {{ $kat->nama }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 pb-10">
                @forelse($menus as $menu)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition cursor-pointer" wire:click="addToCart({{ $menu->id }})">
                        <div class="h-40 bg-gray-100 relative">
                            @if($menu->gambar)
                                <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 flex flex-col justify-between flex-1">
                            <h3 class="font-bold text-gray-800 text-sm mb-2 leading-tight">{{ $menu->nama }}</h3>
                            <div class="flex items-center justify-between mt-auto">
                                <span class="font-bold text-[#046A41] text-sm">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                                <button class="w-8 h-8 rounded-full bg-[#046A41] text-white flex items-center justify-center hover:bg-emerald-800 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center text-gray-400 font-medium">
                        Tidak ada menu yang ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="w-full lg:w-[400px] bg-white h-full border-l border-gray-100 flex flex-col shadow-lg z-20">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Pesanan Aktif</h2>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Draft Order</span>
            </div>

            <div class="p-6 flex-1 overflow-y-auto">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Nama Pelanggan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input wire:model="namaPelanggan" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-lg py-2.5 pl-9 pr-3 focus:outline-none focus:border-[#046A41]" placeholder="Walk-in Customer">
                    </div>
                </div>

                <div class="flex gap-2 mb-6 bg-gray-50 p-1.5 rounded-xl border border-gray-100">
                    <button wire:click="$set('tipePesanan', 'dine_in')" class="flex-1 py-2 rounded-lg text-xs font-bold flex justify-center items-center transition {{ $tipePesanan === 'dine_in' ? 'bg-white text-[#046A41] shadow-sm border border-gray-200' : 'text-gray-500' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg> Dine-in
                    </button>
                    <button wire:click="$set('tipePesanan', 'takeaway')" class="flex-1 py-2 rounded-lg text-xs font-bold flex justify-center items-center transition {{ $tipePesanan === 'takeaway' ? 'bg-white text-[#046A41] shadow-sm border border-gray-200' : 'text-gray-500' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg> Takeaway
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($cart as $id => $item)
                        <div class="flex gap-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200">
                                @if(isset($item['gambar']) && $item['gambar'])
                                    <img src="{{ asset('storage/' . $item['gambar']) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-sm text-gray-800 leading-tight">{{ $item['nama'] }}</h4>
                                    <span class="font-bold text-xs text-[#046A41] whitespace-nowrap ml-2">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex items-center justify-between mt-2">
                                    <p class="text-[10px] text-gray-400 italic">Pajak sudah termasuk dlm total</p>
                                    
                                    <div class="flex items-center border border-gray-200 rounded-md bg-white">
                                        <button wire:click="decrementQty('{{ $id }}')" class="px-2.5 py-1 text-gray-500 hover:text-red-500 transition font-bold">-</button>
                                        <span class="px-2 text-xs font-bold text-gray-800">{{ $item['qty'] }}</span>
                                        <button wire:click="incrementQty('{{ $id }}')" class="px-2.5 py-1 text-gray-500 hover:text-[#046A41] transition font-bold">+</button>
                                    </div>
                                </div>

                                <div class="mt-2.5">
                                    <input wire:model.live.debounce.500ms="cart.{{ $id }}.catatan" type="text" class="w-full text-xs border border-gray-200 rounded-md py-1.5 px-2.5 focus:outline-none focus:border-[#046A41] bg-gray-50 focus:bg-white transition-colors" placeholder="Catatan opsional">
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 opacity-50">
                            <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-sm font-medium text-gray-500">Keranjang masih kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-6 bg-white border-t border-gray-100 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                
                @if (session()->has('error_pesanan'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs font-medium">
                        {{ session('error_pesanan') }}
                    </div>
                @endif

                <div class="flex gap-2 mb-4">
                    <input type="text" class="flex-1 text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-[#046A41]" placeholder="Gunakan kode promo...">
                    <button class="bg-[#FFF5ED] text-[#D66F00] font-bold text-xs px-4 py-2 rounded-lg hover:bg-[#FDECDD] transition">Terapkan</button>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-xs font-medium text-gray-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-medium text-gray-500">
                        <span>Pajak ({{ $pajakPersen }}%)</span>
                        <span>Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                    </div>
                    @if($diskon > 0)
                    <div class="flex justify-between text-xs font-medium text-red-500">
                        <span>Diskon</span>
                        <span>-Rp {{ number_format($diskon, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-black text-gray-900 pt-2 border-t border-gray-100 mt-2">
                        <span>Total Bayar</span>
                        <span class="text-[#046A41]">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button wire:click="prosesPesanan" 
                        class="w-full py-3.5 rounded-xl shadow-md transition flex items-center justify-center font-bold {{ count($cart) > 0 ? 'bg-[#046A41] hover:bg-emerald-800 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                    Lanjut ke Pembayaran
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>

    </main>
</div>