@props(['active' => ''])

<aside class="w-64 bg-white border-r border-gray-100 flex flex-col justify-between h-full z-10 flex-shrink-0">
    <div>
        <div class="px-6 py-8">
            <h1 class="text-xl font-bold text-[#046A41] tracking-wide">Vivalavida Coffee</h1>
            <div class="mt-4">
                <h2 class="text-sm font-bold text-gray-800">Main Branch</h2>
                <p class="text-xs text-gray-500">Jakarta Selatan</p>
            </div>
        </div>

        <nav class="px-4 space-y-2 mt-2">
            <a href="{{ route('kasir.menu') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'menu' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                <span class="text-sm">Menu POS</span>
            </a>

            <a href="{{ route('kasir.pos') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'kasir' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="text-sm">Antrean Kasir</span>
            </a>

            <a href="{{ route('kasir.riwayat') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'riwayat' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm">Riwayat Pesanan</span>
            </a>

            <a href="{{ route('kasir.analytics') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'analytics' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                <span class="text-sm">Laporan Analitik</span>
            </a>

            <div class="pt-4 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Stasiun Produksi</div>

            <a href="{{ route('barista.orders') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'barista' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="text-sm">Antrean Barista</span>
            </a>

            <a href="{{ route('dapur.orders') }}" 
               class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'dapur' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-600 hover:bg-gray-50 font-medium' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                <span class="text-sm">Antrean Makanan</span>
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-xl transition font-medium">
                <svg class="w-5 h-5 mr-3 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="text-sm">Logout</span>
            </button>
        </form>
    </div>
</aside>