<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Antrean Pesanan - Vivalavida</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-800 h-screen flex overflow-hidden">

    <aside class="w-64 bg-[#F8F9FA] border-r border-gray-200 flex flex-col justify-between h-full">
        <div>
            <div class="px-6 py-8">
                <h1 class="text-xl font-bold text-[#0D3B2E]">Vivalavida Coffee</h1>
            </div>

            <div class="px-6 mb-8">
                <h2 class="text-sm font-bold text-gray-800">Main Branch</h2>
                <p class="text-xs text-gray-500">Jakarta Selatan</p>
            </div>

            <nav class="px-4 space-y-2">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-[#0D3B2E] text-white rounded-xl font-medium text-sm shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Orders
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium text-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Menu
                </a>
            </nav>
        </div>

        <div class="px-4 pb-8">
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-xl font-medium text-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col bg-white overflow-hidden">
        
        <header class="px-10 pt-10 pb-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-[#0D3B2E]">Panel Antrean Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pesanan masuk dan status persiapan secara real-time</p>
        </header>

        <div class="flex-1 overflow-y-auto p-10 space-y-4 bg-gray-50/50">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 relative overflow-hidden flex">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#E85D04]"></div>
                
                <div class="p-6 flex w-full">
                    <div class="w-20 pr-4 border-r border-gray-100 flex flex-col justify-center">
                        <span class="text-xs font-bold text-gray-400">NO.</span>
                        <span class="text-2xl font-bold text-gray-800">#17</span>
                    </div>

                    <div class="flex-1 px-6">
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="font-bold text-gray-800">Aditya Wijaya</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600">DARI APLIKASI</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-600">BELUM BAYAR - BAYAR DI KASIR</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Baru saja (14:20)
                        </p>

                        <div class="space-y-1 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">1x Caramel Macchiato <span class="text-xs text-gray-400">(Large, Less Sugar)</span></span>
                                <span class="text-gray-600 font-medium">Rp 45.000</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">1x Almond Croissant</span>
                                <span class="text-gray-600 font-medium">Rp 32.000</span>
                            </div>
                        </div>

                        <div class="flex gap-4 text-xs font-medium">
                            <div class="flex items-center gap-1.5 text-orange-500">
                                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div> Minuman: Sedang Dibuat
                            </div>
                            <div class="flex items-center gap-1.5 text-green-600">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Makanan: Selesai
                            </div>
                        </div>
                    </div>

                    <div class="w-48 pl-6 flex flex-col justify-between items-end border-l border-gray-100">
                        <div class="text-right">
                            <span class="text-xl font-bold text-gray-800">Rp 77.000</span>
                        </div>
                        <button class="w-full py-2 px-4 bg-[#E85D04] hover:bg-[#D05303] text-white text-sm font-bold rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 relative overflow-hidden flex">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#0D3B2E]"></div>
                <div class="p-6 flex w-full">
                    <div class="w-20 pr-4 border-r border-gray-100 flex flex-col justify-center">
                        <span class="text-xs font-bold text-gray-400">NO.</span>
                        <span class="text-2xl font-bold text-[#0D3B2E]">#14</span>
                    </div>
                    <div class="flex-1 px-6">
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="font-bold text-gray-800">Andi Wijaya</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600">DARI APLIKASI</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700">SUDAH BAYAR - QRIS</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            10 Menit yang lalu (14:10)
                        </p>
                        <div class="space-y-1 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">2x Espresso Macchiato <span class="text-xs text-gray-400">(Extra Shot, Oat Milk)</span></span>
                                <span class="text-gray-600 font-medium">Rp 76.000</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">1x Butter Croissant</span>
                                <span class="text-gray-600 font-medium">Rp 28.000</span>
                            </div>
                        </div>
                        <div class="flex gap-4 text-xs font-medium">
                            <div class="flex items-center gap-1.5 text-orange-500">
                                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div> Minuman: Sedang Dibuat
                            </div>
                            <div class="flex items-center gap-1.5 text-orange-500">
                                <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div> Makanan: Sedang Dibuat
                            </div>
                        </div>
                    </div>
                    <div class="w-48 pl-6 flex flex-col justify-between items-end border-l border-gray-100">
                        <div class="text-right">
                            <span class="text-xl font-bold text-gray-800">Rp 104.000</span>
                        </div>
                        <button class="w-full py-2 px-4 bg-[#0D3B2E] hover:bg-[#092B21] text-white text-sm font-bold rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verifikasi & Proses
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-[#E6F0EB]/40 rounded-xl shadow-sm border border-[#0D3B2E]/20 relative overflow-hidden flex">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#0D3B2E]"></div>
                <div class="p-6 flex w-full">
                    <div class="w-20 pr-4 border-r border-[#0D3B2E]/10 flex flex-col justify-center">
                        <span class="text-xs font-bold text-gray-400">NO.</span>
                        <span class="text-2xl font-bold text-[#0D3B2E]">#16</span>
                    </div>
                    <div class="flex-1 px-6">
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="font-bold text-gray-800">Raka Pratama</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600">DARI APLIKASI</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700">SUDAH BAYAR - GOPAY</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            2 Menit yang lalu (14:18)
                        </p>
                        <div class="space-y-1 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">3x Iced Caffe Latte <span class="text-xs text-gray-400">(Less Ice)</span></span>
                                <span class="text-gray-600 font-medium">Rp 105.000</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">2x Truffle Fries</span>
                                <span class="text-gray-600 font-medium">Rp 90.000</span>
                            </div>
                        </div>
                        <div class="flex gap-4 text-xs font-medium">
                            <div class="flex items-center gap-1.5 text-green-600">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Minuman: Selesai
                            </div>
                            <div class="flex items-center gap-1.5 text-green-600">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-600"></div> Makanan: Selesai
                            </div>
                        </div>
                    </div>
                    <div class="w-48 pl-6 flex flex-col justify-between items-end border-l border-[#0D3B2E]/10">
                        <div class="text-right">
                            <span class="text-xl font-bold text-gray-800">Rp 195.000</span>
                        </div>
                        <button class="w-full py-2 px-4 bg-green-700 hover:bg-green-800 text-white text-sm font-bold rounded-lg transition shadow-sm flex flex-col items-center justify-center">
                            <span>Pesanan Lengkap</span>
                            <span class="text-[10px] font-normal opacity-80">Notifikasi Terkirim</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>