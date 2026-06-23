<div class="flex h-screen overflow-hidden w-full bg-[#F9FAFB] font-sans antialiased text-gray-800">
    <x-admin-sidebar active="loyalty" />

    <main class="flex-1 overflow-y-auto p-8 sm:p-10 relative">

        @if (session()->has('message'))
            <div
                class="absolute top-8 right-10 bg-[#E8F5E9] border border-[#046A41] text-[#046A41] px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i> <span class="font-bold text-sm">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div
                class="absolute top-8 right-10 bg-red-50 border border-red-500 text-red-600 px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i> <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#046A41] mb-1">Loyalty & Voucher Program</h1>
                <p class="text-gray-500 text-sm">Atur cara pelanggan mendapatkan poin dan tukarkan dengan voucher
                    diskon.</p>
            </div>
            <button wire:click="bukaModalVoucher"
                class="flex items-center px-5 py-2.5 bg-[#046A41] hover:bg-emerald-800 text-white text-sm font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-ticket mr-2"></i> Buat Katalog Voucher
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center"><i
                            class="fa-solid fa-gear text-[#046A41] mr-2"></i> Point Conversion Rules</h2>
                    <button wire:click="bukaModalRules" class="text-sm font-bold text-blue-600 hover:text-blue-800">Edit
                        Aturan</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nilai Transaksi
                            (Rp)</p>
                        <p class="text-2xl font-black text-gray-900">Rp
                            {{ number_format($nominalTransaksi, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Syarat nominal kelipatan belanja.</p>
                    </div>
                    <div
                        class="p-4 bg-gray-50 rounded-xl border border-gray-200 flex flex-col justify-center relative overflow-hidden">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 z-10">Poin Didapat
                        </p>
                        <p class="text-2xl font-black text-[#046A41] z-10">+ {{ $poinDidapat }} Pts</p>
                        <i class="fa-solid fa-coins absolute -right-4 -bottom-4 text-6xl text-green-100 opacity-50"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-[#046A41] rounded-2xl p-6 text-white shadow-md flex flex-col justify-center relative overflow-hidden">
                <p class="text-xs font-bold text-green-100 uppercase tracking-widest mb-2 z-10">Total Poin Beredar
                    (Pelanggan)</p>
                <h3 class="text-4xl font-black z-10">{{ number_format($totalBeredar, 0, ',', '.') }} <span
                        class="text-lg font-normal">pts</span></h3>
                <div class="mt-4 pt-4 border-t border-green-600 z-10">
                    <p class="text-sm text-green-50"><span class="font-bold">{{ $katalogAktif }}</span> Voucher Aktif di
                        Katalog</p>
                </div>
                <i class="fa-solid fa-star absolute -right-6 -bottom-6 text-8xl text-emerald-800 opacity-40"></i>
            </div>
        </div>

        <h2 class="text-lg font-bold text-gray-900 mb-4">Katalog Voucher (Etalase Reward)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @forelse($vouchers as $v)
                <div
                    class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm relative group hover:border-[#046A41] transition">

                    <div
                        class="absolute top-4 right-4 px-2 py-1 text-[9px] font-black uppercase tracking-widest rounded {{ $v->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                        {{ $v->status }}
                    </div>

                    <div
                        class="w-12 h-12 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center mb-4 border border-orange-100">
                        <i class="fa-solid fa-ticket-simple text-xl"></i>
                    </div>

                    <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">{{ $v->judul }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-4">{{ $v->deskripsi ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-0.5">Potongan</p>
                            <p class="text-sm font-black text-[#046A41]">
                                {{ $v->tipe_diskon == 'persen' ? $v->nilai_diskon . '%' : 'Rp ' . number_format($v->nilai_diskon, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-0.5">Harga</p>
                            <p class="text-sm font-black text-orange-500">{{ $v->poin_dibutuhkan }} pts</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex gap-2">
                        <button wire:click="editVoucher({{ $v->id }})"
                            class="flex-1 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">Edit</button>
                        <button wire:click="hapusVoucher({{ $v->id }})" wire:confirm="Hapus voucher ini dari katalog?"
                            class="w-8 h-8 flex items-center justify-center text-xs font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition"><i
                                class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-16 flex flex-col items-center justify-center bg-white rounded-2xl border border-gray-200 border-dashed">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mb-4"><i
                            class="fa-solid fa-ticket text-2xl"></i></div>
                    <h3 class="text-lg font-bold text-gray-900">Katalog Kosong</h3>
                    <p class="text-sm text-gray-500 mt-1 mb-4">Pelanggan belum bisa menukar poin karena belum ada katalog
                        voucher.</p>
                </div>
            @endforelse
        </div>
        <div class="mb-8">{{ $vouchers->links() }}</div>

        @if($showModalRules)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Edit Point Conversion Rules</h2>
                    <p class="text-sm text-gray-500 mb-6">Tentukan berapa rupiah yang harus dihabiskan pelanggan untuk
                        mendapatkan 1 poin.</p>
                    <form wire:submit.prevent="simpanRules" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Setiap Transaksi Kelipatan
                                (Rp)</label>
                            <input wire:model="nominalTransaksi" type="number"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                            @error('nominalTransaksi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Mendapatkan Poin Sebesar</label>
                            <input wire:model="poinDidapat" type="number"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                            @error('poinDidapat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Pajak (%)</label>
                            <input wire:model="pajakPersen" type="number"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm">
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="tutupModalRules"
                                class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-bold text-white bg-[#046A41] hover:bg-emerald-800 rounded-lg">Simpan
                                Aturan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($showModalVoucher)
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-[#046A41]">
                            {{ $isEditMode ? 'Edit Voucher' : 'Tambah Katalog Voucher' }}
                        </h2>
                        <button wire:click="tutupModalVoucher" class="text-gray-400 hover:text-gray-600"><i
                                class="fa-solid fa-xmark text-xl"></i></button>
                    </div>
                    <div class="p-6 overflow-y-auto space-y-4 flex-1">
                        <form id="voucherForm" wire:submit.prevent="simpanVoucher" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Judul / Nama Voucher</label>
                                <input wire:model="judul" type="text" placeholder="Cth: Diskon Ongkir 10rb"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                                @error('judul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi Singkat</label>
                                <textarea wire:model="deskripsi" rows="2"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Harga (Poin)</label>
                                    <input wire:model="poin_dibutuhkan" type="number" placeholder="Cth: 500"
                                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                                    @error('poin_dibutuhkan') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Berlaku Hingga</label>
                                    <input wire:model="berlaku_hingga" type="date"
                                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41]">
                                    @error('berlaku_hingga') <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 mt-2">
                                <p class="text-xs font-bold text-gray-600 mb-3">Detail Potongan Harga</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Tipe
                                            Diskon</label>
                                        <select wire:model="tipe_diskon"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white">
                                            <option value="nominal">Nominal Rupiah (Rp)</option>
                                            <option value="persen">Persentase (%)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nilai
                                            Potongan</label>
                                        <input wire:model="nilai_diskon" type="number" placeholder="Cth: 15000 atau 10"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                        @error('nilai_diskon') <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1 mt-2">Status Penayangan di
                                    App</label>
                                <select wire:model="status"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:border-[#046A41] bg-white">
                                    <option value="aktif">Aktif (Tampilkan)</option>
                                    <option value="nonaktif">Nonaktif (Sembunyikan)</option>
                                </select>
                            </div>

                        </form>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex gap-3 justify-end">
                        <button type="button" wire:click="tutupModalVoucher"
                            class="px-5 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                        <button type="submit" form="voucherForm"
                            class="px-5 py-2 text-sm font-bold text-white bg-[#046A41] hover:bg-emerald-800 rounded-lg">Simpan
                            Voucher</button>
                    </div>
                </div>
            </div>
        @endif

    </main>
</div>