<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    
    <x-admin-sidebar active="news" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">
        
        @if (session()->has('message'))
            <div class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fa-solid fa-circle-check"></i> <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">News & Content Management</h1>
                <p class="text-gray-500 text-sm">Manage your articles, promotional events, and brand announcements.</p>
            </div>
            <div>
                <button wire:click="bukaModal" class="flex items-center px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                    <i class="fa-solid fa-circle-plus mr-2"></i> Buat Artikel Baru
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><i class="fa-regular fa-file-lines"></i></div>
                    <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded">+12%</span>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Articles</p>
                <h3 class="text-2xl font-black text-gray-900">{{ number_format($totalArticles) }}</h3>
            </div>
            
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-regular fa-eye"></i></div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">+8%</span>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Views</p>
                <h3 class="text-2xl font-black text-gray-900">{{ number_format($totalViews / 1000, 1) }}k</h3>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center"><i class="fa-regular fa-comment-dots"></i></div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded">+5%</span>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Interactions</p>
                <h3 class="text-2xl font-black text-gray-900">{{ number_format($interactions / 1000, 1) }}k</h3>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-share-nodes"></i></div>
                    <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded">+21%</span>
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Shared Count</p>
                <h3 class="text-2xl font-black text-gray-900">{{ $sharedCount }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-t-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full text-sm font-medium border border-gray-200 rounded-lg py-2 pl-9 pr-4 focus:outline-none focus:border-[#046A41]" placeholder="Search articles...">
                </div>
                <select wire:model.live="filterCategory" class="text-sm font-medium border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:border-[#046A41] bg-white text-gray-600">
                    <option value="all">Semua Kategori</option>
                    <option value="Berita">Berita</option>
                    <option value="Promo">Promo</option>
                    <option value="Event">Event</option>
                </select>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                <span>View:</span>
                <div class="flex bg-gray-50 rounded-lg p-1 border border-gray-200">
                    <button wire:click="setViewMode('list')" class="w-8 h-8 rounded flex items-center justify-center {{ $viewMode == 'list' ? 'bg-white shadow-sm text-[#046A41]' : 'text-gray-400 hover:text-gray-600' }}"><i class="fa-solid fa-list"></i></button>
                    <button wire:click="setViewMode('grid')" class="w-8 h-8 rounded flex items-center justify-center {{ $viewMode == 'grid' ? 'bg-white shadow-sm text-[#046A41]' : 'text-gray-400 hover:text-gray-600' }}"><i class="fa-solid fa-border-all"></i></button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-b-2xl shadow-sm border border-t-0 border-gray-100 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50/80">
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Article Title</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Publish Date</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($articles as $article)
                            @php
                                // Penentuan warna kategori
                                $catColor = 'bg-teal-50 text-teal-600';
                                if ($article->kategori == 'Promo') $catColor = 'bg-orange-50 text-orange-600';
                                if ($article->kategori == 'Event') $catColor = 'bg-red-50 text-red-600';

                                // Penentuan warna status
                                $isDraft = $article->status == 'Draft';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex-shrink-0 overflow-hidden border border-gray-100 shadow-sm">
                                            @if($article->gambar)
                                                <img src="{{ asset('storage/' . $article->gambar) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-gray-900 leading-tight mb-0.5">{{ $article->judul }}</h4>
                                            <span class="text-[10px] font-medium text-gray-500">Author: {{ $article->penulis }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-md {{ $catColor }} uppercase tracking-wider">{{ $article->kategori }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-xs font-bold {{ $isDraft ? 'text-gray-500' : 'text-[#046A41]' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isDraft ? 'bg-gray-400' : 'bg-[#046A41]' }} mr-2"></span>
                                        {{ $article->status }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-gray-600">
                                    {{ $isDraft ? '—' : \Carbon\Carbon::parse($article->created_at)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <button wire:click="editArticle({{ $article->id }})" class="text-gray-400 hover:text-blue-600 transition"><i class="fa-solid fa-pen"></i></button>
                                        <button class="text-gray-400 hover:text-[#046A41] transition"><i class="fa-regular fa-eye"></i></button>
                                        <button wire:click="hapusArticle({{ $article->id }})" wire:confirm="Hapus artikel ini?" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada artikel atau konten yang diterbitkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500 font-medium bg-gray-50/50">
                <div>Showing {{ $articles->firstItem() ?? 0 }}-{{ $articles->lastItem() ?? 0 }} of {{ $articles->total() }} entries</div>
                <div>{{ $articles->links() }}</div>
            </div>
        </div>

        @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">{{ $isEditMode ? 'Edit Artikel' : 'Tulis Artikel Baru' }}</h2>
                    <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                
                <form wire:submit.prevent="simpanArticle" class="flex flex-col flex-1 overflow-hidden">
                    
                    <div class="p-6 overflow-y-auto space-y-4 flex-1">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Judul Konten</label>
                            <input wire:model="judul" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Cth: Seni Latte: Lebih Dari Sekedar Susu">
                            @error('judul') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Nama Penulis</label>
                            <input wire:model="penulis" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] focus:outline-none">
                            @error('penulis') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Kategori</label>
                            <select wire:model="kategori" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] bg-white focus:outline-none">
                                <option value="Berita">Berita</option>
                                <option value="Promo">Promo</option>
                                <option value="Event">Event</option>
                            </select>
                        </div>
                    </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Isi Konten / Deskripsi</label>
                            <textarea wire:model="konten" rows="5" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:border-[#046A41] focus:outline-none" placeholder="Ketik isi artikel di sini..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Status Publikasi</label>
                                <select wire:model="status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#046A41] bg-white focus:outline-none">
                                    <option value="Publish">Publish Langsung</option>
                                    <option value="Draft">Simpan Draft</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Gambar Header / Thumbnail</label>
                                <input wire:model="gambar" type="file" accept="image/*" class="w-full text-xs file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-bold file:bg-green-50 file:text-[#046A41] hover:file:bg-green-100">
                                <div wire:loading wire:target="gambar" class="text-xs text-[#046A41] font-bold mt-1">Mengunggah gambar...</div>
                                @error('gambar') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex gap-3 justify-end mt-auto">
                        <button type="button" wire:click="tutupModal" class="px-5 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white font-bold rounded-xl text-sm transition">
                            Simpan Artikel
                        </button>
                    </div>
                </form>

            </div>
        </div>
        @endif

    </main>
</div>