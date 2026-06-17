@props(['active' => ''])

<aside class="w-64 bg-white border-r border-gray-100 flex flex-col justify-between h-full z-10 flex-shrink-0">
    <div class="overflow-y-auto h-full flex flex-col">
        <div class="px-6 py-8 flex items-center">
            <div
                class="w-8 h-8 bg-[#046A41] text-white rounded-lg flex items-center justify-center font-bold text-xl mr-3">
                V</div>
            <div>
                <h1 class="text-lg font-bold text-[#046A41] leading-tight">Vivalavida<br>Coffee</h1>
                <p class="text-[9px] text-gray-400 font-medium tracking-wider uppercase mt-1">Premium Management</p>
            </div>
        </div>

        <nav class="px-4 space-y-1.5 flex-1">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'dashboard' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center mr-3"></i> <span class="text-sm">Dashboard</span>
            </a>
            <a href="{{ route('admin.orders') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'orders' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-cart-shopping w-5 text-center mr-3"></i> <span class="text-sm">Order
                    Management</span>
            </a>
            <a href="{{ route('admin.menus') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'menu-management' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-utensils w-5 text-center mr-3"></i> <span class="text-sm">Menu Management</span>
            </a>
            <a href="{{ route('admin.customers') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'customers' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-users w-5 text-center mr-3"></i> <span class="text-sm">Customer Management</span>
            </a>
            <a href="{{ route('admin.vouchers') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'vouchers' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-ticket w-5 text-center mr-3"></i> <span class="text-sm">Voucher & Promo</span>
            </a>
            <a href="{{ route('admin.loyalty') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'loyalty' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-star w-5 text-center mr-3"></i> <span class="text-sm">Loyalty Program</span>
            </a>
            <a href="{{ route('admin.news') }}"
                class="flex items-center px-4 py-3 rounded-xl transition {{ $active === 'news' ? 'bg-[#046A41] text-white shadow-sm font-semibold' : 'text-gray-500 hover:bg-gray-50 font-medium' }}">
                <i class="fa-solid fa-newspaper w-5 text-center mr-3"></i> <span class="text-sm">News & Content</span>
            </a>
            <a href="#"
                class="flex items-center px-4 py-3 rounded-xl transition text-gray-500 hover:bg-gray-50 font-medium">
                <i class="fa-solid fa-chart-line w-5 text-center mr-3"></i> <span class="text-sm">Reports &
                    Analytics</span>
            </a>
            <a href="#"
                class="flex items-center px-4 py-3 rounded-xl transition text-gray-500 hover:bg-gray-50 font-medium">
                <i class="fa-solid fa-gear w-5 text-center mr-3"></i> <span class="text-sm">System Settings</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100 mt-4 space-y-2">
            <a href="{{ route('kasir.menu') }}"
                class="flex items-center justify-center w-full px-4 py-3 bg-[#046A41] hover:bg-emerald-800 text-white rounded-xl shadow-md transition font-bold text-sm mb-4">
                <i class="fa-solid fa-plus mr-2"></i> New Order
            </a>

            <a href="#"
                class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition font-medium">
                <i class="fa-regular fa-circle-user w-5 text-center mr-3"></i> <span class="text-sm">Profile</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center w-full px-4 py-2 text-red-500 hover:bg-red-50 rounded-xl transition font-medium">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center mr-3"></i> <span
                        class="text-sm">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>