<div class="flex h-screen overflow-hidden w-full bg-[#F3F4F6] font-sans antialiased text-gray-800">

    <x-admin-sidebar active="dashboard" />

    <main class="flex-1 overflow-y-auto p-8">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Dashboard Summary</h1>
                <p class="text-gray-500 text-sm">Welcome back, Admin. Here's what's happening today.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i
                            class="fa-solid fa-magnifying-glass text-xs"></i></span>
                    <input type="text"
                        class="text-sm border border-gray-200 rounded-full py-2.5 pl-9 pr-4 focus:outline-none w-64 shadow-sm"
                        placeholder="Search data...">
                </div>
                <button
                    class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-[#046A41] shadow-sm transition">
                    <i class="fa-regular fa-bell"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl border-t-4 border-[#046A41] shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Total Revenue</span>
                    <div class="w-8 h-8 bg-green-50 text-[#046A41] rounded-lg flex items-center justify-center"><i
                            class="fa-solid fa-money-bill-wave text-xs"></i></div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <span class="text-xs font-bold text-[#046A41]"><i class="fa-solid fa-arrow-trend-up mr-1"></i> +12.5%
                    <span class="font-medium text-gray-400">vs last month</span></span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-t-4 border-gray-700 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Total Orders</span>
                    <div class="w-8 h-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center"><i
                            class="fa-solid fa-basket-shopping text-xs"></i></div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">{{ number_format($totalOrders) }}</h3>
                <span class="text-xs font-bold text-[#046A41]"><i class="fa-solid fa-arrow-trend-up mr-1"></i> +8.2%
                    <span class="font-medium text-gray-400">vs last month</span></span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-t-4 border-red-800 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">New Customers</span>
                    <div class="w-8 h-8 bg-red-50 text-red-800 rounded-lg flex items-center justify-center"><i
                            class="fa-solid fa-user-plus text-xs"></i></div>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">{{ number_format($newCustomers) }}</h3>
                <span class="text-xs font-bold text-red-600"><i class="fa-solid fa-arrow-trend-down mr-1"></i> -2.4%
                    <span class="font-medium text-gray-400">vs last month</span></span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-t-4 border-teal-500 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Top Product</span>
                    <div class="w-8 h-8 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center"><i
                            class="fa-solid fa-star text-xs"></i></div>
                </div>
                <h3 class="text-lg font-black text-gray-900 mb-2 truncate">{{ $topProductData->nama ?? 'N/A' }}</h3>
                <span class="text-xs font-medium text-gray-500">Sold {{ $topProductData->total_terjual ?? 0 }} units
                    today</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-lg font-bold text-gray-900">7-Day Revenue Analytics</h2>
                    <select
                        class="text-xs font-bold border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none text-gray-600">
                        <option>Last 7 Days</option>
                    </select>
                </div>
                <div class="flex items-end justify-between flex-1 gap-4 pt-4">
                    @foreach($chartData as $data)
                        <div class="flex-1 flex flex-col items-center justify-end h-40 group cursor-pointer relative">
                            <div
                                class="absolute -top-8 bg-gray-800 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10 pointer-events-none">
                                Rp {{ number_format($data['rev'] / 1000, 0) }}k
                            </div>
                            <div
                                class="w-full max-w-[40px] bg-gray-100 rounded-t-md relative flex flex-col justify-end overflow-hidden h-full">
                                <div class="w-full bg-[#046A41] rounded-t-md transition-all duration-700 group-hover:bg-emerald-500"
                                    style="height: {{ min(100, $data['height']) }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-400 mt-3">{{ $data['hari'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">Order Source</h2>
                    <div class="flex items-center">
                        <div class="w-20 h-20 rounded-full relative mr-6 flex-shrink-0"
                            style="background: conic-gradient(#046A41 {{ $pctApp }}%, #E2E8F0 0);">
                            <div class="absolute inset-2 bg-white rounded-full"></div>
                        </div>
                        <div class="space-y-3 w-full">
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center font-bold text-gray-700"><span
                                        class="w-2 h-2 rounded-full bg-[#046A41] mr-2"></span> Mobile App</div>
                                <span class="font-black">{{ $pctApp }}%</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center font-bold text-gray-700"><span
                                        class="w-2 h-2 rounded-full bg-gray-200 mr-2"></span> Walk-in</div>
                                <span class="font-black">{{ $pctWalkIn }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-5">Payment Methods</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5"><span
                                    class="text-gray-700">Digital / QRIS</span><span>{{ $pctQris }}%</span></div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#046A41]" style="width: {{ $pctQris }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5"><span
                                    class="text-gray-700">Cash</span><span>{{ $pctTunai }}%</span></div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gray-400" style="width: {{ $pctTunai }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
                    <a href="{{ route('kasir.riwayat') }}" class="text-xs font-bold text-[#046A41] hover:underline">View
                        All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Order ID</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-black text-gray-900 text-xs">#ORD-{{ $order->id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-700 text-xs">{{ $order->nama_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500 truncate max-w-[150px]">
                                            @foreach($order->items as $item) {{ $item->jumlah }}x
                                            {{ $item->nama }}@if(!$loop->last), @endif @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($order->status == 'completed')
                                            <span
                                                class="px-2 py-1 bg-green-50 text-[#046A41] text-[9px] font-bold rounded uppercase">Completed</span>
                                        @elseif($order->status == 'preparing' || $order->status == 'ready')
                                            <span
                                                class="px-2 py-1 bg-blue-50 text-blue-600 text-[9px] font-bold rounded uppercase">Processing</span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-orange-50 text-orange-600 text-[9px] font-bold rounded uppercase">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="font-bold text-gray-900 text-xs">Rp
                                            {{ number_format($order->total_akhir, 0, ',', '.') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-sm text-gray-400">No recent orders.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-900">Top Products</h2>
                </div>
                <div class="p-6 flex-1 space-y-5">
                    @forelse($topProductsList as $prod)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden mr-3">
                                    @if($prod->gambar)
                                        <img src="{{ asset('storage/' . $prod->gambar) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800">{{ $prod->nama }}</h4>
                                    <p class="text-[10px] text-gray-400">{{ $prod->terjual }} Orders</p>
                                </div>
                            </div>
                            <span
                                class="text-xs font-black text-[#046A41]">+Rp{{ number_format($prod->pendapatan / 1000, 0) }}k</span>
                        </div>
                    @empty
                        <div class="text-center text-sm text-gray-400">No product data.</div>
                    @endforelse
                </div>
                <div class="p-4 border-t border-gray-50">
                    <button wire:click="exportReport" wire:loading.attr="disabled"
                        class="w-full py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition flex items-center justify-center">
                        <span wire:loading.remove>Export Report</span>
                        <span wire:loading class="text-[#046A41]">
                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Exporting...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </main>
</div>