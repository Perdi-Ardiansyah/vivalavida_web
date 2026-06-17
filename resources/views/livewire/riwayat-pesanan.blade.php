<div class="flex h-screen overflow-hidden w-full bg-[#FAFAFA] font-sans antialiased text-gray-800 relative">
    
    <x-sidebar active="riwayat" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 print:hidden">
        
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#046A41] mb-2">Riwayat Pesanan</h1>
                <p class="text-gray-500 text-sm">Menampilkan 50 data transaksi terakhir yang sudah selesai atau dibatalkan.</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
            
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-xs font-medium border border-gray-200 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-[#046A41] bg-[#F9FAFB]" placeholder="Cari Nama atau No. ID...">
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button wire:click="$set('statusFilter', 'semua')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'semua' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Semua</button>
                    <button wire:click="$set('statusFilter', 'completed')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'completed' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Selesai</button>
                    <button wire:click="$set('statusFilter', 'cancelled')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'cancelled' ? 'bg-red-600 text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Dibatalkan</button>
                </div>
            </div>

            <button wire:click="exportCSV" class="w-full md:w-auto flex items-center justify-center px-5 py-2.5 bg-white border-2 border-[#046A41] text-[#046A41] text-xs font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Laporan
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold">ID / Tanggal</th>
                            <th class="px-6 py-4 font-bold">Pelanggan</th>
                            <th class="px-6 py-4 font-bold">Ringkasan Item</th>
                            <th class="px-6 py-4 font-bold">Total</th>
                            <th class="px-6 py-4 font-bold text-center">Status</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pesanans as $pesanan)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-black text-[#046A41] text-sm mb-1">#{{ str_pad($pesanan->id, 3, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($pesanan->updated_at)->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-gray-900">{{ $pesanan->nama_pelanggan ?? 'Walk-in Customer' }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">{{ str_replace('_', ' ', $pesanan->tipe_pesanan) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-600 truncate max-w-[200px]">
                                        @foreach($pesanan->items as $item)
                                            {{ $item->jumlah }}x {{ $item->nama }}@if(!$loop->last), @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-gray-900">Rp {{ number_format($pesanan->total_akhir, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-gray-500 uppercase">{{ $pesanan->metode_pembayaran ?? 'Cash' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($pesanan->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-1 bg-[#E8F5E9] text-[#046A41] text-[10px] font-bold rounded-md uppercase border border-green-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-md uppercase border border-red-100">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="bukaModalStruk({{ $pesanan->id }})" class="inline-flex items-center text-xs font-bold text-[#046A41] hover:text-emerald-600 transition bg-[#E8F5E9] hover:bg-[#D1E8D5] px-3 py-2 rounded-md">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Struk
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                    Tidak ada data riwayat yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    @if($showModalStruk && $pesananStruk)
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <div class="flex justify-between items-center mb-4 print:hidden text-white">
                <h2 class="text-lg font-bold">Detail Pesanan</h2>
                <button wire:click="tutupModalStruk" class="hover:text-gray-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <div id="struk-print-area" class="bg-white rounded-t-2xl shadow-xl overflow-hidden print:shadow-none print:w-full">
                <div class="p-6 text-center border-b border-gray-100 border-dashed">
                    <h2 class="text-xl font-bold text-[#046A41]">Vivalavida Coffee</h2>
                    <p class="text-xs text-gray-500 mt-1">Main Branch, Jakarta Selatan</p>
                    <p class="text-[10px] text-gray-400 mt-1">ID Transaksi: #VV-{{ date('Ymd') }}-{{ str_pad($pesananStruk->id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="p-6">
                    <div class="space-y-2 mb-5 border-b border-gray-100 border-dashed pb-5">
                        <div class="flex justify-between text-xs"><span class="text-gray-500">Waktu</span><span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($pesananStruk->created_at)->translatedFormat('d M Y, H:i') }}</span></div>
                        <div class="flex justify-between text-xs"><span class="text-gray-500">Pelanggan</span><span class="font-bold text-gray-800">{{ $pesananStruk->nama_pelanggan ?? 'Walk-in Customer' }}</span></div>
                        <div class="flex justify-between text-xs"><span class="text-gray-500">Status Akhir</span>
                            <span class="font-bold {{ $pesananStruk->status === 'completed' ? 'text-[#046A41]' : 'text-red-600' }} uppercase">
                                {{ $pesananStruk->status === 'completed' ? 'Selesai' : 'Dibatalkan' }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-3 mb-5 border-b border-gray-100 border-dashed pb-5">
                        @foreach($pesananStruk->items as $item)
                            @php $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null; @endphp
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-xs">{{ $item->jumlah }}x {{ $item->nama }}</h4>
                                    @if($opsi) <p class="text-[9px] text-gray-500 mt-0.5">{{ implode(', ', (array) $opsi) }}</p> @endif
                                </div>
                                <span class="font-bold text-xs text-gray-800">Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1.5 mb-5">
                        <div class="flex justify-between text-xs text-gray-600"><span>Subtotal</span><span>Rp {{ number_format($modalSubtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-xs text-gray-600"><span>Pajak (10%)</span><span>Rp {{ number_format($modalPajak, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-sm font-black text-[#046A41] pt-3"><span>Total</span><span>Rp {{ number_format($pesananStruk->total_akhir, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2.5 flex justify-between items-center print:border print:border-gray-200">
                        <span class="text-xs text-gray-500 font-medium">Metode Pembayaran</span>
                        <span class="font-bold text-gray-800 text-xs uppercase">{{ $pesananStruk->metode_pembayaran ?? 'TUNAI' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="h-3 bg-repeat-x print:hidden" style="background-image: radial-gradient(circle at 6px 0, transparent 7px, white 8px); background-size: 12px 12px; background-position: bottom;"></div>

            <button onclick="window.print()" class="w-full mt-4 bg-[#046A41] hover:bg-emerald-800 text-white font-bold py-3.5 rounded-xl shadow-md transition flex justify-center items-center print:hidden">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak Struk
            </button>
        </div>
    </div>
    @endif
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #struk-print-area, #struk-print-area * { visibility: visible; }
        #struk-print-area { position: absolute; left: 0; top: 0; width: 80mm; margin: 0; padding: 0; box-shadow: none; }
        .print\:hidden { display: none !important; }
    }
</style>