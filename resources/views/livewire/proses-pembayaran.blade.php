<div class="min-h-screen bg-[#FAFAFA] font-sans text-gray-800 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8 print:bg-white print:py-0">
    
    @if(!$is_success)
        <div class="max-w-6xl w-full mx-auto print:hidden">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#046A41]">Vivalavida Coffee</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-6">
                    <a href="{{ route('kasir.pos') }}" class="inline-flex items-center text-[#046A41] font-semibold mb-6 hover:underline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Antrean
                    </a>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <div class="space-y-6 mb-8 max-h-[400px] overflow-y-auto pr-2">
                            @foreach($items as $item)
                                @php
                                    $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null;
                                @endphp
                                <div class="flex gap-4 items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-xl flex-shrink-0 border border-gray-100 overflow-hidden">
                                        @if($item->gambar)
                                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm">{{ $item->nama }}</h3>
                                        <p class="text-[11px] text-gray-500 mt-0.5">
                                            @if($opsi) {{ implode(' • ', (array) $opsi) }} @endif
                                        </p>
                                        <div class="font-bold text-sm text-gray-900 mt-1">
                                            {{ $item->jumlah }}x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-[#F9FAFB] rounded-xl p-5 border border-gray-100">
                            <div class="flex justify-between text-sm text-gray-600 mb-2 font-medium">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mb-4 font-medium">
                                <span>Pajak (10%)</span>
                                <span>Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-[#046A41] pt-4 border-t border-gray-200">
                                <span>Total</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 mt-12 lg:mt-0 flex flex-col">
                    <h2 class="text-xl font-bold text-[#046A41] mb-6">Pembayaran Tunai</h2>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex-1 flex flex-col justify-between">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal Uang Diterima</label>
                            <div class="relative mb-4">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 font-bold text-gray-500">Rp</span>
                                <input wire:model.live.debounce.300ms="uang_diterima" type="number" class="w-full text-2xl font-bold border border-gray-200 rounded-xl py-4 pl-12 pr-4 focus:outline-none focus:border-[#046A41] focus:ring-1 focus:ring-[#046A41]" placeholder="0">
                            </div>

                            <div class="grid grid-cols-3 gap-3 mb-8">
                                <button wire:click="setUangDiterima({{ $total }})" class="py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-bold text-gray-700 transition border border-gray-200">Uang Pas</button>
                                <button wire:click="setUangDiterima(50000)" class="py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-bold text-gray-700 transition border border-gray-200">50.000</button>
                                <button wire:click="setUangDiterima(100000)" class="py-2.5 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-bold text-gray-700 transition border border-gray-200">100.000</button>
                            </div>

                            <div class="bg-[#FFF5ED] border border-[#FDECDD] rounded-xl p-6 mb-8 text-center">
                                <span class="block text-sm font-bold text-[#D66F00] mb-2 uppercase tracking-wide">Kembalian Pelanggan</span>
                                <span class="text-4xl font-black text-[#EF7D00]">Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button wire:click="konfirmasiPembayaran" class="w-full bg-[#046A41] hover:bg-emerald-800 text-white font-bold py-4 rounded-xl shadow-md transition flex justify-center items-center">
                            Konfirmasi & Cetak Struk
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else


    <div class="max-w-md w-full mx-auto">
            
            <div class="text-center mb-8 print:hidden">
                <div class="w-16 h-16 bg-[#046A41] rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Pesanan Berhasil!</h1>
                <p class="text-gray-500 text-sm">Transaksi Anda telah selesai diproses.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6 print:border-none print:shadow-none print:rounded-none">
                
                <div class="p-8 text-center border-b border-gray-100 border-dashed">
                    <h2 class="text-xl font-bold text-[#046A41]">Vivalavida Coffee</h2>
                    <p class="text-xs text-gray-500 mt-1">Main Branch, Jakarta Selatan</p>
                    <p class="text-[10px] text-gray-400 mt-1">ID Transaksi: #VV-{{ date('Ymd') }}-{{ str_pad($pesanan_id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="p-8">
                    <div class="space-y-3 mb-6 border-b border-gray-100 border-dashed pb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Waktu</span>
                            <span class="font-medium text-gray-800">{{ $waktu_selesai }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Kasir</span>
                            <span class="font-medium text-gray-800">{{ auth()->user()->name ?? 'Staff Kasir' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="font-medium text-gray-800">{{ $pesanan->nama_pelanggan ?? 'Walk-in Customer' }}</span>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6 border-b border-gray-100 border-dashed pb-6">
                        @foreach($items as $item)
                            @php
                                $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null;
                            @endphp
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $item->jumlah }}x {{ $item->nama }}</h4>
                                    @if($opsi)
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ implode(', ', (array) $opsi) }}</p>
                                    @endif
                                </div>
                                <span class="font-medium text-sm text-gray-800">Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Pajak (10%)</span>
                            <span>Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-[#046A41] pt-4">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-[#F2F9F3] border border-[#B7E0C2] rounded-lg p-3 flex justify-between items-center print:bg-transparent print:border-gray-300">
                        <div class="flex items-center text-[#046A41] text-sm font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Metode Bayar
                        </div>
                        <span class="font-bold text-[#046A41] text-sm uppercase">TUNAI</span>
                    </div>
                </div>

                <div class="h-4 bg-repeat-x print:hidden" style="background-image: radial-gradient(circle at 10px 0, transparent 12px, white 13px); background-size: 20px 20px; background-position: bottom;"></div>
            </div>

            <div class="space-y-3 print:hidden">
                <button onclick="window.print()" class="w-full bg-[#046A41] hover:bg-emerald-800 text-white font-bold py-3.5 rounded-xl shadow-md transition flex justify-center items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Struk
                </button>
                <button wire:click="pesananBaru" class="w-full bg-white hover:bg-gray-50 text-[#046A41] font-bold py-3.5 rounded-xl border-2 border-[#046A41] transition flex justify-center items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Pesanan Baru
                </button>
            </div>

        </div>
    @endif
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .max-w-md, .max-w-md * { visibility: visible; }
        .max-w-md { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
        .print\:hidden { display: none !important; }
    }
</style>