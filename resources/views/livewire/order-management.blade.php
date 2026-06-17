<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="orders" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Order Management</h1>
                <p class="text-gray-500 text-sm">Manage and track your customer orders in real-time</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="exportCSV" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl shadow-sm hover:bg-gray-50 transition">
                    <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
                </button>
                <button class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl shadow-sm hover:bg-gray-50 transition">
                    <i class="fa-solid fa-file-pdf mr-2"></i> Export PDF
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
            <div class="relative flex-1 min-w-[250px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-xl py-2.5 pl-10 pr-4 focus:outline-none focus:border-[#046A41] shadow-sm bg-white" placeholder="Search by Order ID or Customer Name">
            </div>
            
            <select wire:model.live="dateRange" class="text-sm font-medium border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#046A41] shadow-sm bg-white text-gray-600">
                <option value="all">Date Range (All)</option>
                <option value="today">Today</option>
                <option value="7_days">Last 7 Days</option>
                <option value="30_days">Last 30 Days</option>
            </select>

            <select wire:model.live="statusFilter" class="text-sm font-medium border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#046A41] shadow-sm bg-white text-gray-600">
                <option value="all">Status (All)</option>
                <option value="completed">Selesai</option>
                <option value="preparing">Diproses</option>
                <option value="cancelled">Dibatalkan</option>
            </select>

            <select wire:model.live="sourceFilter" class="text-sm font-medium border border-gray-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#046A41] shadow-sm bg-white text-gray-600">
                <option value="all">Source (All)</option>
                <option value="dine_in">Dine-In</option>
                <option value="takeaway">Takeaway</option>
            </select>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">NO</th>
                            <th class="px-6 py-4">ORDER ID</th>
                            <th class="px-6 py-4">CUSTOMER</th>
                            <th class="px-6 py-4">ITEMS</th>
                            <th class="px-6 py-4">TOTAL</th>
                            <th class="px-6 py-4">PAYMENT</th>
                            <th class="px-6 py-4">SOURCE</th>
                            <th class="px-6 py-4">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($orders as $index => $order)
                            @php
                                // Membuat inisial nama untuk Avatar
                                $words = explode(' ', $order->nama_pelanggan);
                                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                
                                // Menentukan warna status
                                $statusLabel = 'DIPROSES';
                                $statusColor = 'bg-yellow-50 text-yellow-600 border-yellow-100';
                                $statusDot = 'bg-yellow-500';
                                
                                if ($order->status == 'completed') {
                                    $statusLabel = 'SELESAI';
                                    $statusColor = 'bg-[#E8F5E9] text-[#046A41] border-green-200';
                                    $statusDot = 'bg-[#046A41]';
                                } elseif ($order->status == 'cancelled') {
                                    $statusLabel = 'DIBATALKAN';
                                    $statusColor = 'bg-red-50 text-red-600 border-red-100';
                                    $statusDot = 'bg-red-500';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ $orders->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-bold text-[#046A41] text-sm">#VC-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0">
                                            {{ $initials }}
                                        </div>
                                        <span class="font-bold text-gray-800 text-sm whitespace-nowrap">{{ $order->nama_pelanggan }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-orange-500 text-sm">{{ $order->total_items ?? 0 }} Items</td>
                                <td class="px-6 py-4 font-bold text-gray-900 text-sm whitespace-nowrap">Rp {{ number_format($order->total_akhir, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded uppercase border border-gray-200">
                                        {{ $order->metode_pembayaran ?? 'CASH' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-600 text-sm capitalize">{{ str_replace('_', '-', $order->tipe_pesanan) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 {{ $statusColor }} text-[10px] font-bold rounded-md uppercase border whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }} mr-1.5"></span> {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-400 font-medium">No orders found matching the criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border-l-4 border-[#046A41] shadow-sm flex justify-between items-center relative overflow-hidden">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">TODAY'S REVENUE</p>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">Rp {{ number_format($todaysRevenue, 0, ',', '.') }}</h3>
                    <span class="text-xs font-bold {{ $revenueGrowth >= 0 ? 'text-[#046A41]' : 'text-red-500' }}">
                        <i class="fa-solid {{ $revenueGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1"></i> 
                        {{ number_format(abs($revenueGrowth), 1) }}% <span class="font-medium text-gray-400">from yesterday</span>
                    </span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-[#046A41] rounded-full flex items-center justify-center text-xl z-10"><i class="fa-solid fa-money-bill-wave"></i></div>
            </div>

            <div class="bg-white p-6 rounded-2xl border-l-4 border-yellow-500 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ACTIVE ORDERS</p>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">{{ $activeOrders }} Orders</h3>
                    <span class="text-xs font-medium text-gray-400">Currently being prepared</span>
                </div>
                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center text-xl"><i class="fa-solid fa-stopwatch"></i></div>
            </div>

            <div class="bg-white p-6 rounded-2xl border-l-4 border-red-800 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">SUCCESS RATE</p>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">{{ number_format($successRate, 1) }}%</h3>
                    <span class="text-xs font-medium text-gray-400">Based on last 30 days</span>
                </div>
                <div class="w-12 h-12 bg-red-50 text-red-800 rounded-full flex items-center justify-center text-xl"><i class="fa-solid fa-circle-check"></i></div>
            </div>
        </div>

    </main>
</div>