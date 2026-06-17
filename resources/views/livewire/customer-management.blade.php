<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="customers" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">
        
        @if (session()->has('message'))
            <div class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-circle-check"></i> <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error_message'))
            <div class="absolute top-8 right-10 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-ban"></i> <span class="font-bold text-sm">{{ session('error_message') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Customer Management</h1>
                <p class="text-gray-500 text-sm">Manage your regular coffee enthusiasts and loyalty members.</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden relative cursor-pointer border-2 border-white shadow-sm">
                <img src="https://ui-avatars.com/api/?name=Admin&background=046A41&color=fff" alt="Admin" class="w-full h-full object-cover">
                <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-bold text-gray-400 mb-1.5 ml-1">Search Customer</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-xl py-2.5 pl-9 pr-4 focus:outline-none focus:border-[#046A41]" placeholder="Name or email address...">
                </div>
            </div>
            
            <div class="w-40">
                <label class="block text-[11px] font-bold text-gray-400 mb-1.5 ml-1">Registration Date</label>
                <div class="relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"><i class="fa-regular fa-calendar"></i></span>
                    <input type="text" class="w-full text-sm font-medium border border-gray-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-[#046A41] text-gray-600 bg-gray-50 cursor-not-allowed" placeholder="dd/mm/yyyy" disabled>
                </div>
            </div>

            <div class="w-48">
                <label class="block text-[11px] font-bold text-gray-400 mb-1.5 ml-1">Orders Range</label>
                <select wire:model.live="orderRange" class="w-full text-sm font-medium border border-gray-200 rounded-xl py-2.5 px-3 focus:outline-none focus:border-[#046A41] text-gray-700 bg-white">
                    <option value="all">All Orders</option>
                    <option value="high">VIP (>50 Orders)</option>
                    <option value="medium">Regular (10-49 Orders)</option>
                    <option value="low">New (<10 Orders)</option>
                </select>
            </div>

            <button class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition flex items-center shadow-sm">
                <i class="fa-solid fa-filter mr-2"></i> More Filters
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#046A41] text-white">
                        <tr class="text-[10px] font-black uppercase tracking-widest">
                            <th class="px-6 py-4 rounded-tl-2xl">PHOTO & NAME</th>
                            <th class="px-6 py-4">CONTACT INFO</th>
                            <th class="px-6 py-4">ROLE</th>
                            <th class="px-6 py-4 text-center">TOTAL ORDERS</th>
                            <th class="px-6 py-4 text-center">POINTS</th>
                            <th class="px-6 py-4">REG. DATE</th>
                            <th class="px-6 py-4 text-center rounded-tr-2xl">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $user)
                            @php
                                $orders = $user->total_orders ?? 0;
                                $tier = 'New Member';
                                $tierColor = 'text-[#046A41]';
                                
                                if ($orders >= 100) {
                                    $tier = 'VIP Member'; $tierColor = 'text-purple-600';
                                } elseif ($orders >= 50) {
                                    $tier = 'Premium Gold'; $tierColor = 'text-yellow-600';
                                } elseif ($orders >= 10) {
                                    $tier = 'Silver Tier'; $tierColor = 'text-gray-500';
                                }

                                $points = floor(($user->total_spent ?? 0) / 1000);
                                $words = explode(' ', $user->name);
                                $avatarName = urlencode($words[0]);

                                // Penentuan warna lencana Role
                                $roleStr = strtolower($user->role ?? 'customer');
                                if ($roleStr === 'admin') {
                                    $roleBadge = 'bg-red-50 text-red-600 border-red-100';
                                } elseif (in_array($roleStr, ['kasir', 'cashier', 'dapur', 'barista'])) {
                                    $roleBadge = 'bg-blue-50 text-blue-600 border-blue-100';
                                } else {
                                    $roleBadge = 'bg-gray-50 text-gray-500 border-gray-200';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 mr-4 flex-shrink-0 overflow-hidden border border-gray-100">
                                            <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background=random" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-gray-900 leading-tight">{{ $user->name }}</h4>
                                            <span class="text-[10px] font-bold {{ $tierColor }} mt-0.5 block">{{ $tier }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-600 font-medium mb-0.5">{{ $user->email }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-md border uppercase tracking-wider {{ $roleBadge }}">
                                        {{ $user->role ?? 'Customer' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="font-black text-sm text-gray-900">{{ number_format($orders) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="font-bold text-sm text-gray-700">{{ number_format($points) }}</div>
                                    <div class="text-[9px] font-bold text-gray-400 uppercase">pts</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-gray-700">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button wire:click="lihatDetail({{ $user->id }})" class="text-[#046A41] hover:text-emerald-800 transition bg-green-50 w-8 h-8 rounded-full flex items-center justify-center">
                                            <i class="fa-regular fa-eye text-xs"></i>
                                        </button>
                                        <button wire:click="blokirPelanggan({{ $user->id }})" wire:confirm="Batasi akses pengguna ini?" class="text-red-500 hover:text-red-700 transition bg-red-50 w-8 h-8 rounded-full flex items-center justify-center">
                                            <i class="fa-solid fa-ban text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-medium">No customers found matching your search.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500 font-medium bg-gray-50/50">
                <div>Showing {{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }} of {{ number_format($customers->total()) }} users</div>
                <div>{{ $customers->links() }}</div>
            </div>
        </div>

        <div class="text-center pb-4">
            <span class="text-gray-300 font-black text-sm uppercase tracking-widest">Vivalavida Management Portal</span>
        </div>

        @if($showDetailModal && $selectedCustomer)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-address-card text-xl text-[#046A41]"></i>
                        <h2 class="text-lg font-bold text-gray-900">Profil & Rekap Data Pelanggan</h2>
                    </div>
                    <button wire:click="tutupDetailModal" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <div class="p-6 overflow-y-auto space-y-6 flex-1">
                    
                    <div class="flex flex-col sm:flex-row items-center gap-5 bg-gradient-to-r from-green-50/50 to-white p-5 rounded-2xl border border-green-100/50">
                        <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-white shadow-md flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedCustomer->name) }}&background=046A41&color=fff&size=128" class="w-full h-full object-cover">
                        </div>
                        <div class="text-center sm:text-left flex-1 space-y-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $selectedCustomer->name }}</h3>
                            <p class="text-sm font-medium text-gray-500">{{ $selectedCustomer->email }}</p>
                            
                            <div class="flex flex-wrap gap-2 pt-1.5 justify-center sm:justify-start items-center">
                                <span class="px-2 py-0.5 bg-[#046A41] text-white text-[9px] font-black rounded uppercase tracking-wider">
                                    {{ $selectedCustomer->role ?? 'Customer' }}
                                </span>
                                <span class="text-xs text-gray-400 font-medium">
                                    Terdaftar sejak {{ \Carbon\Carbon::parse($selectedCustomer->created_at)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Kunjungan</span>
                            <span class="text-xl font-black text-gray-900">{{ number_format($selectedCustomer->total_orders ?? 0) }}x</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Loyalitas</span>
                            <span class="text-xl font-black text-orange-500">{{ number_format(floor(($selectedCustomer->total_spent ?? 0) / 1000)) }} Pts</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Belanja</span>
                            <span class="text-sm font-black text-[#046A41] pt-1 block">Rp {{ number_format($selectedCustomer->total_spent ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">5 Transaksi Terakhir</h4>
                        <div class="border border-gray-100 rounded-xl overflow-hidden bg-white shadow-sm">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 text-gray-500 font-bold uppercase">
                                    <tr>
                                        <th class="p-3">ID Nota</th>
                                        <th class="p-3">Tanggal</th>
                                        <th class="p-3">Metode</th>
                                        <th class="p-3 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                    @forelse($selectedCustomer->riwayat_pesanans as $order)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="p-3 font-bold text-[#046A41]">#ORD-{{ $order->id }}</td>
                                            <td class="p-3 text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</td>
                                            <td class="p-3 uppercase text-gray-400 text-[10px] font-bold"><span class="bg-gray-100 px-1.5 py-0.5 rounded border">{{ $order->metode_pembayaran ?? 'Cash' }}</span></td>
                                            <td class="p-3 text-right font-bold text-gray-900">Rp {{ number_format($order->total_akhir, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-gray-400">Belum ada riwayat transaksi digital di kafe ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button wire:click="tutupDetailModal" class="px-5 py-2 bg-[#046A41] hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Tutup Ringkasan
                    </button>
                </div>

            </div>
        </div>
        @endif

    </main>
</div>