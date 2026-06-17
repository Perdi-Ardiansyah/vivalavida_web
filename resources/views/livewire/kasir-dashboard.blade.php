<div wire:poll.5s class="flex h-screen overflow-hidden w-full bg-[#FAFAFA] font-sans antialiased text-gray-800 relative">
    
    <x-sidebar active="kasir" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 print:hidden">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#046A41] mb-2">Panel Antrean Pesanan</h1>
                <p class="text-gray-500 text-sm">Kelola pesanan masuk dan status persiapan secara real-time</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-col lg:flex-row gap-4 justify-between items-center">
            <div class="relative w-full lg:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-xs font-medium border border-gray-200 rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-[#046A41] focus:ring-1 focus:ring-[#046A41] bg-[#F9FAFB]" placeholder="Cari Pelanggan atau No. Antrean...">
            </div>

            <div class="flex flex-wrap gap-1.5 w-full lg:w-auto overflow-x-auto">
                <button wire:click="$set('statusFilter', 'semua')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'semua' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Semua Antrean</button>
                <button wire:click="$set('statusFilter', 'belum_bayar')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'belum_bayar' ? 'bg-[#EF7D00] text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Belum Bayar</button>
                <button wire:click="$set('statusFilter', 'perlu_verifikasi')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'perlu_verifikasi' ? 'bg-[#046A41] text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Perlu Verifikasi</button>
                <button wire:click="$set('statusFilter', 'sedang_disiapkan')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'sedang_disiapkan' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Sedang Disiapkan</button>
                <button wire:click="$set('statusFilter', 'siap_diambil')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $statusFilter === 'siap_diambil' ? 'bg-green-600 text-white shadow-sm' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }}">Siap Diambil</button>
            </div>

            <div class="w-full lg:w-44">
                <select wire:model.live="tipeFilter" class="w-full text-xs font-bold border border-gray-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-[#046A41] bg-[#F9FAFB] text-gray-700">
                    <option value="semua">Semua Tipe</option>
                    <option value="dine_in">Dine-In</option>
                    <option value="takeaway">Takeaway</option>
                </select>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($pesanans as $pesanan)
                @php
                    $statusDb = $pesanan->status ?? 'new';
                    $statusBayarDb = strtolower($pesanan->status_pembayaran ?? '');
                    
                    $isReady = $statusDb === 'ready';
                    $isNew = $statusDb === 'new';
                    $isSudahBayar = in_array($statusBayarDb, ['sudah_bayar', 'paid', 'lunas']);
                    $isBelumBayar = !$isSudahBayar;
                    $isPerluVerifikasi = ($isSudahBayar && $isNew);
                    
                    $cardBg = $isReady ? 'bg-[#F2F9F3] border-[#B7E0C2]' : 'bg-white border-gray-200';
                @endphp

                <div class="rounded-xl shadow-sm border {{ $cardBg }} p-6 flex flex-col md:flex-row justify-between gap-6 transition-colors">
                    <div class="flex gap-6 flex-1">
                        <div class="flex flex-col items-center pt-1 w-12 flex-shrink-0">
                            <span class="text-[10px] text-gray-400 font-bold tracking-wider">NO.</span>
                            <span class="text-xl font-black text-[#046A41]">#{{ $pesanan->id }}</span>
                        </div>

                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2.5 mb-2">
                                <h3 class="text-base font-bold text-gray-900">{{ $pesanan->nama_pelanggan ?? 'Walk-in Customer' }}</h3>
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded-md uppercase border border-blue-100">
                                    {{ $pesanan->sumber_pesanan == 'app' ? 'DARI APLIKASI' : 'MANUAL KASIR' }}
                                </span>
                                @if($isBelumBayar)
                                    <span class="px-2 py-0.5 bg-orange-50 text-[#E85D04] text-[10px] font-bold rounded-md uppercase border border-orange-100">BELUM BAYAR - KASIR</span>
                                @else
                                    <span class="px-2 py-0.5 bg-[#E8F5E9] text-[#046A41] text-[10px] font-bold rounded-md uppercase border border-green-200">SUDAH BAYAR - {{ strtoupper($pesanan->metode_pembayaran ?? 'ONLINE') }}</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center text-xs text-gray-500 mb-5 font-medium">
                                <svg class="w-3.5 h-3.5 mr-1.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ \Carbon\Carbon::parse($pesanan->created_at)->diffForHumans() }} ({{ \Carbon\Carbon::parse($pesanan->created_at)->format('H:i') }})                            </div>

                            <div class="space-y-2 mb-6">
                                @foreach($pesanan->items as $item)
                                    @php $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null; @endphp
                                    <div class="flex justify-between items-start text-sm">
                                        <span class="text-gray-800 pr-4">
                                            <span class="font-bold text-gray-900 mr-1">{{ $item->jumlah }}x</span> {{ $item->nama }} 
                                            @if($opsi) <span class="text-gray-500 text-[11px] ml-1">({{ implode(', ', (array) $opsi) }})</span> @endif
                                        </span>
                                        <span class="text-gray-700 font-medium whitespace-nowrap">Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex items-center gap-5 text-[11px] font-bold">
                                @if($pesanan->has_minuman)
                                    @php
                                        $dotMinuman = $pesanan->status_dapur === 'menunggu' ? 'bg-orange-500' : ($pesanan->status_dapur === 'sedang_dibuat' ? 'bg-yellow-500' : 'bg-[#046A41]');
                                        $textMinuman = $pesanan->status_dapur === 'menunggu' ? 'text-orange-500' : ($pesanan->status_dapur === 'sedang_dibuat' ? 'text-yellow-600' : 'text-[#046A41]');
                                        $labelMinuman = str_replace('_', ' ', $pesanan->status_dapur ?? 'Menunggu');
                                    @endphp
                                    <div class="flex items-center {{ $textMinuman }} capitalize"><span class="w-1.5 h-1.5 rounded-full {{ $dotMinuman }} mr-2"></span> Minuman: {{ $labelMinuman }}</div>
                                @endif

                                @if($pesanan->has_makanan)
                                    @php
                                        $dotMakanan = $pesanan->status_makanan === 'menunggu' ? 'bg-orange-500' : ($pesanan->status_makanan === 'sedang_dimasak' ? 'bg-yellow-500' : 'bg-[#046A41]');
                                        $textMakanan = $pesanan->status_makanan === 'menunggu' ? 'text-orange-500' : ($pesanan->status_makanan === 'sedang_dimasak' ? 'text-yellow-600' : 'text-[#046A41]');
                                        $labelMakanan = str_replace('_', ' ', $pesanan->status_makanan ?? 'Menunggu');
                                    @endphp
                                    <div class="flex items-center {{ $textMakanan }} capitalize"><span class="w-1.5 h-1.5 rounded-full {{ $dotMakanan }} mr-2"></span> Makanan: {{ $labelMakanan }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-end justify-between min-w-[200px]">
                        <div class="text-right mb-4 flex flex-col items-end">
                            <span class="text-xl font-bold text-gray-900">Rp {{ number_format($pesanan->total_akhir, 0, ',', '.') }}</span>
                            <button wire:click="bukaModalStruk({{ $pesanan->id }})" class="mt-2 flex items-center text-xs font-bold text-[#046A41] hover:text-emerald-600 transition bg-[#E8F5E9] hover:bg-[#D1E8D5] px-3 py-1.5 rounded-md">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Detail & Struk
                            </button>
                        </div>

                        @if($isReady)
                            <button wire:click="pesananSelesai({{ $pesanan->id }})" class="w-full bg-[#046A41] hover:bg-emerald-800 text-white rounded-lg py-2.5 px-4 shadow-sm transition flex flex-col items-center justify-center">
                                <span class="font-bold text-sm">Pesanan Lengkap</span>
                            </button>
                        @elseif($isBelumBayar)
                            <button wire:click="bukaModalPembayaran({{ $pesanan->id }})" class="w-full bg-[#EF7D00] hover:bg-[#D66F00] text-white rounded-lg py-3 px-4 shadow-sm transition flex items-center justify-center text-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="font-bold text-sm">Proses Pembayaran</span>
                            </button>
                        @elseif($isPerluVerifikasi)
                            <button wire:click="bukaModalVerifikasi({{ $pesanan->id }})" class="w-full bg-[#046A41] hover:bg-emerald-800 text-white rounded-lg py-3 px-4 shadow-sm transition flex items-center justify-center">
                                <span class="font-bold text-sm">Verifikasi & Proses</span>
                            </button>
                        @else
                            <button disabled class="w-full bg-gray-100 text-gray-400 font-bold py-3 px-4 rounded-lg text-sm cursor-not-allowed border border-gray-200">
                                Sedang Disiapkan
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-16 text-center border border-gray-100 flex flex-col justify-center items-center">
                    <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p class="text-gray-400 font-medium">Tidak ada pesanan yang cocok dengan filter aktif.</p>
                </div>
            @endforelse
        </div>
    </main>

    @if($showModalPembayaran && $pesananPembayaran)
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 print:hidden">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative">
            <div class="bg-[#F8FAFC] p-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-[#046A41]">Pembayaran Tunai</h2>
                    <p class="text-sm text-gray-500">Antrean #{{ $pesananPembayaran->id }}</p>
                </div>
                <button wire:click="tutupModalPembayaran" class="text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6">
                <div class="mb-6 bg-[#F2F9F3] border border-[#B7E0C2] rounded-xl p-4 flex justify-between items-center">
                    <span class="text-sm font-semibold text-[#046A41]">Total Tagihan</span>
                    <span class="text-2xl font-black text-[#046A41]">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</span>
                </div>

                <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal Uang Diterima</label>
                <div class="relative mb-4">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 font-bold text-gray-500">Rp</span>
                    <input wire:model.live.debounce.300ms="uangDiterima" type="number" class="w-full text-xl font-bold border border-gray-200 rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-[#046A41] focus:ring-1 focus:ring-[#046A41]" placeholder="0">
                </div>

                <div class="grid grid-cols-3 gap-3 mb-6">
                    <button wire:click="setUangDiterima({{ $totalPembayaran }})" class="py-2.5 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm font-bold text-gray-700 transition border border-gray-200">Uang Pas</button>
                    <button wire:click="setUangDiterima(50000)" class="py-2.5 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm font-bold text-gray-700 transition border border-gray-200">50.000</button>
                    <button wire:click="setUangDiterima(100000)" class="py-2.5 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm font-bold text-gray-700 transition border border-gray-200">100.000</button>
                </div>

                <div class="bg-[#FFF5ED] border border-[#FDECDD] rounded-xl p-4 mb-6 text-center">
                    <span class="block text-[11px] font-bold text-[#D66F00] mb-1 uppercase tracking-wide">Kembalian Pelanggan</span>
                    <span class="text-3xl font-black text-[#EF7D00]">Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
                </div>

                <button wire:click="konfirmasiPembayaran" class="w-full bg-[#046A41] hover:bg-emerald-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl shadow-md transition flex justify-center items-center" {{ ((int) str_replace('.', '', $uangDiterima) < $totalPembayaran) ? 'disabled' : '' }}>
                    Konfirmasi & Cetak Struk
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($showModalVerifikasi && $pesananVerifikasi)
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 print:hidden">
        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl flex flex-col md:flex-row overflow-hidden max-h-[90vh]">
            <div class="bg-[#F8FAFC] w-full md:w-2/5 p-8 flex flex-col relative border-r border-gray-100">
                <div>
                    <h2 class="text-xl font-bold text-[#046A41] mb-2">Bukti Bayar</h2>
                    <p class="text-sm text-gray-500 mb-8">Verifikasi detail transaksi digital</p>
                    <div class="inline-flex items-center px-4 py-2 bg-[#E8F5E9] text-[#046A41] rounded-full font-bold text-sm mb-10 border border-green-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Status: Lunas
                    </div>
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="mb-5 flex justify-between items-start">
                            <div>
                                <span class="block text-[11px] font-bold text-gray-400 tracking-wider mb-1 uppercase">ID Transaksi</span>
                                <span class="font-bold text-gray-900">#VV-{{ date('Ymd') }}-{{ str_pad($pesananVerifikasi->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold text-gray-400 tracking-wider mb-1 uppercase">Metode Pembayaran</span>
                            <div class="flex items-center text-gray-800 font-bold uppercase">{{ $pesananVerifikasi->metode_pembayaran }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white w-full md:w-3/5 p-8 flex flex-col relative overflow-hidden">
                <button wire:click="tutupModal" class="absolute top-6 right-6 text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="mb-6">
                    <span class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Pesanan Dari</span>
                    <h2 class="text-2xl font-bold text-gray-900 mt-1">{{ $pesananVerifikasi->nama_pelanggan ?? 'Walk-in Customer' }}</h2>
                </div>
                <div class="flex-1 overflow-y-auto pr-2 space-y-4 mb-6">
                    @foreach($pesananVerifikasi->items as $item)
                        @php $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null; @endphp
                        <div class="flex gap-4 items-center">
                            <div class="w-1.5 h-8 bg-gray-200 rounded"></div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 text-sm">{{ $item->nama }}</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">@if($opsi) {{ implode(' • ', (array) $opsi) }} @endif</p>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-sm text-gray-900"><span class="font-bold text-gray-500 mr-2">{{ $item->jumlah }}x</span> Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2 font-medium"><span>Subtotal</span><span>Rp {{ number_format($modalSubtotal, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-sm text-gray-600 mb-4 font-medium"><span>Pajak (10%)</span><span>Rp {{ number_format($modalPajak, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-xl font-bold text-[#046A41] pt-4 border-t border-gray-200 mb-6"><span>Total</span><span>Rp {{ number_format($pesananVerifikasi->total_akhir, 0, ',', '.') }}</span></div>
                    <div class="flex gap-3">
                        <button wire:click="teruskanKeDapur({{ $pesananVerifikasi->id }})" class="flex-1 bg-[#046A41] hover:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center text-sm">Konfirmasi & Teruskan ke Dapur</button>
                        <button wire:click="tolakPesanan({{ $pesananVerifikasi->id }})" class="px-5 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 font-bold rounded-xl transition flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg> Cetak Struk Sekarang
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