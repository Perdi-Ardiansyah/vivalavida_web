<div wire:poll.5s class="flex h-screen overflow-hidden w-full bg-[#F8FAFC] font-sans antialiased text-gray-800">
    
    <x-sidebar active="barista" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-[#046A41] mb-2">Panel Antrean Minuman</h1>
                <p class="text-gray-500 text-sm">Memproses {{ count($pesanans) }} pesanan aktif saat ini</p>
            </div>
            <div class="flex gap-3">
                <div class="bg-white border border-gray-200 px-4 py-2 rounded-full flex items-center text-sm font-semibold shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 mr-2"></span>
                    <span class="text-gray-700">{{ $countSedangDibuat }} Sedang Dibuat</span>
                </div>
                <div class="bg-white border border-gray-200 px-4 py-2 rounded-full flex items-center text-sm font-semibold shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                    <span class="text-gray-700">{{ $countMenunggu }} Menunggu</span>
                </div>
            </div>
        </div>

        <div class="space-y-5">
            @forelse($pesanans as $pesanan)
                @php
                    $isSedangDibuat = $pesanan->status_dapur === 'sedang_dibuat';
                    
                    // Styling dinamis
                    $cardBorder = $isSedangDibuat ? 'border-l-4 border-l-[#046A41]' : 'border-l-4 border-l-gray-200';
                    $tipePesanan = str_replace('_', ' ', $pesanan->tipe_pesanan);
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 {{ $cardBorder }} p-6 flex flex-col lg:flex-row justify-between gap-6 transition-all">
                    
                    <div class="flex gap-6 flex-1">
                        <div class="bg-gray-50 rounded-xl p-4 flex flex-col items-center justify-center w-24 h-24 flex-shrink-0 border border-gray-100">
                            <span class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Antrean</span>
                            <span class="text-2xl font-black text-[#046A41]">#{{ $pesanan->id }}</span>
                        </div>

                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $pesanan->nama_pelanggan ?? 'Walk-in Customer' }}</h3>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        @if($isSedangDibuat)
                                            <span class="px-2.5 py-1 bg-[#FFF5ED] text-[#D66F00] text-[10px] font-bold rounded-md flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Sedang Dibuat
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-md flex items-center">
                                                Menunggu
                                            </span>
                                        @endif
                                        
                                        <span class="px-2.5 py-1 bg-[#F2F9F3] text-[#046A41] text-[10px] font-bold rounded-md capitalize">
                                            {{ $tipePesanan }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs font-medium text-gray-500 text-right">
                                    Pesan: {{ \Carbon\Carbon::parse($pesanan->created_at)->format('H:i') }} 
                                    ({{ \Carbon\Carbon::parse($pesanan->created_at)->diffForHumans(null, true, true) }} ago)
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 mt-4">
                                @foreach($pesanan->items as $item)
                                    @php
                                        $opsi = $item->opsi_tambahan ? json_decode($item->opsi_tambahan, true) : null;
                                    @endphp
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">
                                            {{ $item->jumlah }}x {{ $item->nama }}
                                        </h4>
                                        @if($opsi)
                                            <ul class="mt-1 space-y-0.5">
                                                @foreach((array)$opsi as $op)
                                                    <li class="text-[11px] text-gray-500 italic flex items-center">
                                                        <span class="w-1 h-1 rounded-full bg-gray-300 mr-1.5"></span> {{ $op }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($pesanan->catatan)
                                    <div class="md:col-span-2 text-[11px] text-red-500 font-medium bg-red-50 p-2 rounded-md border border-red-100 mt-1">
                                        Catatan: {{ $pesanan->catatan }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 min-w-[180px] justify-center border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                        @if($isSedangDibuat)
                            <button disabled class="w-full bg-white text-gray-400 border border-gray-200 font-bold py-3 px-4 rounded-xl text-sm flex items-center justify-center cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Mulai Buat
                            </button>
                            <button wire:click="minumanSelesai({{ $pesanan->id }})" class="w-full bg-[#046A41] hover:bg-emerald-800 text-white font-bold py-3 px-4 rounded-xl shadow-md transition flex items-center justify-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Minuman Selesai
                            </button>
                        @else
                            <button wire:click="mulaiBuat({{ $pesanan->id }})" class="w-full bg-white hover:bg-gray-50 text-[#046A41] border border-[#046A41] font-bold py-3 px-4 rounded-xl transition flex items-center justify-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Mulai Buat
                            </button>
                            <button disabled class="w-full bg-[#82B5A0] text-white/80 font-bold py-3 px-4 rounded-xl text-sm flex items-center justify-center cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Minuman Selesai
                            </button>
                        @endif
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl p-16 text-center border border-gray-100 flex flex-col justify-center items-center">
                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Dapur Sedang Kosong</h3>
                    <p class="text-gray-500 text-sm">Belum ada pesanan minuman yang masuk ke antrean.</p>
                </div>
            @endforelse
        </div>
    </main>
</div>