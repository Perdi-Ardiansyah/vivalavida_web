<div class="flex h-screen overflow-hidden w-full bg-[#FAFAFA] font-sans antialiased text-gray-800">
    
    <x-sidebar active="analytics" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10">
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Laporan Analitik</h1>
                <p class="text-gray-500 text-sm">Pantau performa penjualan Vivalavida secara real-time.</p>
            </div>
            <div class="flex bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm">
                <button wire:click="setFilter('hari_ini')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filter == 'hari_ini' ? 'bg-[#046A41] text-white shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">Hari Ini</button>
                <button wire:click="setFilter('7_hari')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filter == '7_hari' ? 'bg-[#046A41] text-white shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">7 Hari Terakhir</button>
                <button wire:click="setFilter('bulan_ini')" class="px-4 py-2 text-xs font-bold rounded-lg transition {{ $filter == 'bulan_ini' ? 'bg-[#046A41] text-white shadow-md' : 'text-gray-500 hover:bg-gray-50' }}">Bulan Ini</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
                <h3 class="text-xl font-black text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center text-[10px] font-bold text-[#046A41] bg-green-50 w-fit px-2 py-0.5 rounded-md">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg> 12.5%
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-5 text-[#046A41]"><i class="fa-solid fa-wallet text-6xl"></i></div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pesanan</p>
                <h3 class="text-xl font-black text-gray-900">{{ $totalPesanan }}</h3>
                <div class="mt-2 flex items-center text-[10px] font-bold text-[#046A41] bg-green-50 w-fit px-2 py-0.5 rounded-md"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg> 8.2%</div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Pesanan</p>
                <h3 class="text-xl font-black text-gray-900">Rp {{ number_format($rataRataPesanan, 0, ',', '.') }}</h3>
                <div class="mt-2 flex items-center text-[10px] font-bold text-[#046A41] bg-green-50 w-fit px-2 py-0.5 rounded-md"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg> 4.1%</div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Customer Baru</p>
                <h3 class="text-xl font-black text-gray-900">{{ $customerBaru }}</h3>
                <div class="mt-2 flex items-center text-[10px] font-bold text-[#046A41] bg-green-50 w-fit px-2 py-0.5 rounded-md"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg> 18%</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-10">
                    <h2 class="text-xl font-bold text-gray-900">Tren Penjualan</h2>
                </div>
                <div class="flex items-end justify-between h-64 gap-2">
                    @foreach($trenPenjualan as $data)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-[#046A41]/10 rounded-t-lg relative flex flex-col justify-end overflow-hidden" style="height: 100%">
                                <div class="bg-[#046A41] w-full rounded-t-lg transition-all duration-700" style="height: {{ $data['tinggi'] }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-400 mt-4">{{ $data['hari'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-8">Metode Pembayaran</h2>
                <div class="flex flex-col items-center">
                    <div class="w-48 h-48 rounded-full flex items-center justify-center relative" 
                         style="background: conic-gradient(#046A41 {{ $persenQris }}%, #E2E8F0 {{ $persenQris }}% 100%);">
                        <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center shadow-inner">
                            <div class="text-center">
                                <span class="block text-2xl font-black text-[#046A41]">{{ round($persenQris) }}%</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase">Digital</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 w-full space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-[#046A41] mr-3"></span> <span class="text-xs font-bold text-gray-600">QRIS / Digital</span></div>
                            <span class="text-xs font-black text-gray-900">{{ round($persenQris) }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-gray-200 mr-3"></span> <span class="text-xs font-bold text-gray-600">Tunai</span></div>
                            <span class="text-xs font-black text-gray-900">{{ round($persenTunai) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Produk Terlaris</h2>
                <button class="text-xs font-bold text-[#046A41] hover:underline">Lihat Semua</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-8 py-4">Produk</th>
                            <th class="px-8 py-4">Kategori</th>
                            <th class="px-8 py-4">Unit Terjual</th>
                            <th class="px-8 py-4">Pendapatan</th>
                            <th class="px-8 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($produkTerlaris as $produk)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 mr-4 overflow-hidden">
                                        @if($produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <span class="font-bold text-sm text-gray-800">{{ $produk->nama }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5"><span class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full">{{ $produk->kategori }}</span></td>
                            <td class="px-8 py-5 font-black text-sm text-gray-700">{{ number_format($produk->total_terjual) }}</td>
                            <td class="px-8 py-5 font-black text-sm text-gray-900">Rp {{ number_format($produk->total_pendapatan, 0, ',', '.') }}</td>
                            <td class="px-8 py-5 text-right"><i class="fa-solid fa-chevron-right text-gray-300"></i></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>