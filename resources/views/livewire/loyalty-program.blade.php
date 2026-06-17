<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="loyalty" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">
        
        @if (session()->has('message'))
            <div class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-circle-check"></i> <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Loyalty Program & Rewards</h1>
                <p class="text-gray-500 text-sm">Configure your brand's points ecosystem and manage available redemptions.</p>
            </div>
            <div class="flex gap-3">
                <button class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl shadow-sm hover:bg-gray-50 transition flex items-center">
                    <i class="fa-solid fa-download mr-2"></i> Export Data
                </button>
                <button wire:click="bukaModal" class="px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition flex items-center">
                    <i class="fa-solid fa-circle-plus mr-2"></i> Create Reward
                </button>
            </div>
        </div>

        <div class="flex flex-col xl:flex-row gap-6 mb-8">
            <div class="flex-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-[#046A41] flex items-center"><i class="fa-solid fa-gear mr-2"></i> Point Conversion Rules</h2>
                    <button wire:click="bukaModalRules" class="text-xs font-bold text-blue-600 hover:underline">Edit Rules</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-[#046A41]/5 opacity-0 group-hover:opacity-100 transition"></div>
                        <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">EARNINGS RATE</span>
                        
                        <div class="flex items-center gap-2 relative z-10">
                            <span class="text-xl font-black text-gray-900">Rp {{ number_format($aturanPoin->nominal_transaksi ?? 10000, 0, ',', '.') }}</span>
                            <i class="fa-solid fa-arrow-right text-gray-300"></i>
                            <span class="text-xl font-black text-[#046A41]">{{ $aturanPoin->poin_didapat ?? 1 }} pt</span>
                        </div>
                        
                        <p class="text-[10px] text-gray-500 mt-2 font-medium relative z-10">Pelanggan mendapatkan poin setiap mencapai nominal kelipatan di atas.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">MEMBER MULTIPLIER</span>
                        <div class="flex items-center gap-2">
                            <div class="text-lg font-black text-yellow-600 leading-tight">Gold<br>Tier</div>
                            <i class="fa-solid fa-arrow-trend-up text-orange-400 ml-1"></i>
                            <span class="text-2xl font-black text-orange-500">1.5x</span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1 font-medium">Bonus points multiplier for premium members.</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">POINT EXPIRY</span>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-black text-gray-900">12</span>
                            <span class="text-sm font-bold text-gray-600 mt-1">Months</span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2 font-medium">Rolling expiration from date of transaction.</p>
                    </div>
                </div>
            </div>

            <div class="w-full xl:w-80 bg-[#046A41] p-6 rounded-2xl shadow-sm relative overflow-hidden text-white flex flex-col justify-between">
                <div class="absolute -right-6 -top-6 opacity-10"><i class="fa-solid fa-star text-9xl"></i></div>
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-green-100 mb-2">TOTAL CIRCULATING POINTS</h3>
                    <div class="flex items-end gap-1">
                        <span class="text-4xl font-black">{{ number_format($totalPoints) }}</span>
                        <span class="text-sm font-bold text-green-200 mb-1">pts</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                        <span class="block text-[8px] font-black uppercase tracking-widest text-green-200 mb-1">REDEEMED THIS MONTH</span>
                        <span class="text-lg font-bold">42.8k</span>
                    </div>
                    <div class="bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                        <span class="block text-[8px] font-black uppercase tracking-widest text-green-200 mb-1">NEW ISSUANCE</span>
                        <span class="text-lg font-bold">+124k</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Available Reward Items</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($rewards as $item)
                    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col relative group hover:shadow-md transition">
                        <div class="absolute top-6 right-6 z-10 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $item->status == 'active' ? 'bg-[#046A41] text-white' : 'bg-gray-500 text-white' }}">
                            {{ $item->status }}
                        </div>
                        
                        <div class="h-40 rounded-xl bg-gray-100 mb-4 overflow-hidden relative">
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fa-solid fa-gift text-4xl"></i></div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-gray-900 leading-tight mb-1">{{ $item->nama }}</h3>
                            <p class="text-[10px] text-gray-500 line-clamp-2 mb-4 h-7">{{ $item->deskripsi }}</p>
                            
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-bold text-gray-400">Points Needed</span>
                                <span class="text-xs font-black text-[#046A41]">{{ number_format($item->poin_dibutuhkan) }} pts</span>
                            </div>
                            
                            <div class="mb-5">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[10px] font-bold text-gray-400">Stock Level</span>
                                    <span class="text-[10px] font-bold {{ $item->stok > 20 ? 'text-gray-900' : 'text-red-500' }}">{{ $item->stok }}</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $item->stok > 20 ? 'bg-[#046A41]' : 'bg-red-500' }}" style="width: {{ min(100, ($item->stok / 500) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <button wire:click="editReward({{ $item->id }})" class="w-full py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                            Manage Reward
                        </button>
                    </div>
                @endforeach

                <div wire:click="bukaModal" class="border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center p-6 text-gray-400 hover:text-[#046A41] hover:border-[#046A41] hover:bg-green-50/50 transition cursor-pointer min-h-[350px]">
                    <div class="w-12 h-12 rounded-full bg-white border border-gray-100 flex items-center justify-center mb-4 shadow-sm">
                        <i class="fa-solid fa-plus text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-700 mb-1">New Reward</h3>
                    <p class="text-[10px] text-center px-4 leading-relaxed font-medium">Click to add a new item to the loyalty catalog.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Recent Redemptions</h2>
                    <p class="text-xs text-gray-500">Real-time log of customer reward activity.</p>
                </div>
            </div>
            <div class="overflow-x-auto p-8 text-center text-gray-500">
                @if(count($redemptions) > 0)
                    @else
                    Belum ada riwayat penukaran barang fisik dari pelanggan.
                @endif
            </div>
        </div>

        @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-[#046A41]">{{ $isEditMode ? 'Edit Reward Item' : 'New Reward Item' }}</h2>
                    <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="simpanReward" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Nama Reward</label>
                        <input wire:model="nama" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]" placeholder="Cth: Tumbler Exclusive">
                        @error('nama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                        <textarea wire:model="deskripsi" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-orange-600 mb-1">Points Needed</label>
                            <input wire:model="poin_dibutuhkan" type="number" class="w-full border border-orange-200 bg-orange-50/30 rounded-lg px-4 py-2 text-sm focus:border-orange-500">
                            @error('poin_dibutuhkan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Stock Level</label>
                            <input wire:model="stok" type="number" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                            @error('stok') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                            <select wire:model="status" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Gambar</label>
                            <input wire:model="gambar" type="file" class="w-full text-xs">
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        @if($isEditMode)
                            <button type="button" wire:click="hapusReward({{ $rewardId }})" wire:confirm="Hapus reward ini?" class="w-10 border border-red-200 text-red-500 rounded-xl hover:bg-red-50 flex items-center justify-center transition"><i class="fa-solid fa-trash"></i></button>
                        @endif
                        <button type="button" wire:click="tutupModal" class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition text-sm">Batal</button>
                        <button type="submit" class="flex-1 py-2.5 bg-[#046A41] text-white font-bold rounded-xl hover:bg-emerald-800 transition text-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($showRulesModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-lg font-bold text-[#046A41] flex items-center"><i class="fa-solid fa-gear mr-2"></i> Konversi Poin</h2>
                    <button wire:click="tutupModalRules" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="simpanRules" class="p-6 space-y-5">
                    
                    <div class="bg-green-50/50 p-4 rounded-xl border border-[#046A41]/20">
                        <p class="text-xs text-[#046A41] font-medium leading-relaxed">
                            Atur rasio uang yang harus dibelanjakan pelanggan untuk mendapatkan poin.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Nominal Belanja (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-bold text-sm">Rp</span>
                            <input wire:model="nominalTransaksi" type="number" class="w-full border border-gray-200 rounded-lg py-2.5 pl-9 pr-4 text-sm font-bold focus:border-[#046A41] focus:ring focus:ring-green-100 transition" placeholder="Cth: 10000">
                        </div>
                        @error('nominalTransaksi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-center">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-arrow-down"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Poin yang Didapat</label>
                        <div class="relative">
                            <input wire:model="poinDidapat" type="number" class="w-full border border-gray-200 rounded-lg py-2.5 px-4 text-sm font-bold text-[#046A41] focus:border-[#046A41] focus:ring focus:ring-green-100 transition" placeholder="Cth: 1">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#046A41] font-black text-sm">Pts</span>
                        </div>
                        @error('poinDidapat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2.5 bg-[#046A41] text-white font-bold rounded-xl hover:bg-emerald-800 transition shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </main>
</div>