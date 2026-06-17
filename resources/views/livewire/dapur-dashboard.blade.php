<div wire:poll.5s class="flex h-screen overflow-hidden w-full bg-[#FAFAFA] font-sans antialiased text-gray-800">
    
    <x-sidebar active="dapur" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Antrean Pesanan</h1>
                <p class="text-gray-500 text-sm">Menampilkan pesanan makanan saja (diurutkan dari terlama).</p>
            </div>
            <div>
                <div class="bg-gray-100 border border-gray-200 px-4 py-2.5 rounded-xl flex items-center text-sm font-bold text-gray-600">
                    Total Antrean: <span class="ml-2 text-gray-900">{{ count($pesanans) }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($pesanans as $pesanan)
                @php
                    $isDimasak = $pesanan->status_makanan === 'sedang_dimasak';
                    
                    // Kalkulasi waktu (Menit)
                    $waktuPesan = \Carbon\Carbon::parse($pesanan->created_at);
                    $menitBerlalu = $waktuPesan->diffInMinutes(now());
                    $isTerlambat = $menitBerlalu >= 15 && !$isDimasak;

                    // Styling Warna Border
                    $borderColor = $isDimasak ? 'border-l-[#046A41]' : ($isTerlambat ? 'border-l-red-600' : 'border-l-gray-300');
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 border-l-[6px] {{ $borderColor }} p-6 flex flex-col lg:flex-row justify-between gap-6">
                    
                    <div class="flex gap-6 flex-1">
                        <div class="bg-[#F2F9F3] rounded-xl p-4 flex flex-col items-center justify-center w-24 h-24 flex-shrink-0">
                            <span class="text-[10px] text-gray-500 font-bold tracking-widest uppercase">NO. ANTRE</span>
                            <span class="text-3xl font-black text-[#046A41]">{{ str_pad($pesanan->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $pesanan->nama_pelanggan ?? 'Walk-in Customer' }}</h3>
                                    <div class="flex items-center text-xs font-medium text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @if($menitBerlalu == 0) Baru Saja @else {{ $menitBerlalu }} Menit Lalu @endif
                                        
                                        @if($isTerlambat)
                                            <span class="ml-3 px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold rounded uppercase">Hampir Terlambat</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($isDimasak)
                                    <span class="text-[#D66F00] text-xs font-bold uppercase tracking-wider bg-[#FFF5ED] px-3 py-1 rounded-full">Sedang Dimasak</span>
                                @else
                                    <span class="text-gray-500 text-xs font-bold uppercase tracking-wider bg-gray-100 px-3 py-1 rounded-full">Menunggu</span>
                                @endif
                            </div>

                            <div class="space-y-3">
                                @foreach($pesanan->items as $item)
                                    @php $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null; @endphp
                                    <div class="flex gap-4 p-3 bg-gray-50/50 rounded-xl border border-gray-100">
                                        <div class="w-8 h-8 bg-[#E8F5E9] text-[#046A41] font-bold rounded-lg flex items-center justify-center flex-shrink-0">
                                            {{ $item->jumlah }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $item->nama }}</h4>
                                            @if($opsi)
                                                <p class="text-[11px] text-red-500 italic mt-0.5 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ implode(', ', (array) $opsi) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 min-w-[200px] justify-center border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                        @if($isDimasak)
                            <button disabled class="w-full bg-[#F2F9F3] text-[#046A41] font-bold py-3.5 px-4 rounded-xl text-sm flex items-center justify-center cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Sedang Dimasak
                            </button>
                            <button wire:click="makananSelesai({{ $pesanan->id }})" class="w-full bg-[#046A41] hover:bg-emerald-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition flex items-center justify-center text-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Makanan Selesai
                            </button>
                        @else
                            <button wire:click="mulaiMasak({{ $pesanan->id }})" class="w-full bg-white hover:bg-gray-50 text-[#046A41] border-2 border-[#046A41] font-bold py-3.5 px-4 rounded-xl transition flex items-center justify-center text-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Mulai Masak
                            </button>
                            <button disabled class="w-full bg-gray-200 text-gray-400 font-bold py-3.5 px-4 rounded-xl text-sm flex items-center justify-center cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Makanan Selesai
                            </button>
                        @endif
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl p-16 text-center border border-gray-100 flex flex-col justify-center items-center">
                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Dapur Sedang Kosong</h3>
                    <p class="text-gray-500 text-sm">Belum ada pesanan makanan yang masuk ke antrean koki.</p>
                </div>
            @endforelse
        </div>
    </main>
</div>