@php
    $activePeriod = $periods->where('is_active', true)->first();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="bg-rose-100 text-rose-600 p-2 rounded-xl text-xl shadow-sm">📤</span>
            <h2 class="font-black text-2xl text-gray-800 leading-tight">
                {{ __('Input Barang Keluar (Manual/Darurat)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm font-bold flex items-center gap-3 mb-6 animate-fade-in-down">
                    <span class="text-xl">✅</span> <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl shadow-sm font-bold flex items-center gap-3 mb-6 animate-fade-in-down">
                    <span class="text-xl">⚠️</span> <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-5 sm:p-6 mb-6 shadow-sm flex items-start gap-4">
                <div class="bg-white p-2.5 rounded-xl text-2xl shadow-sm shrink-0 border border-amber-100">💡</div>
                <div>
                    <h3 class="font-black text-amber-900 text-lg">Kapan fitur ini digunakan?</h3>
                    <p class="text-sm font-medium text-amber-800 mt-1 leading-relaxed">
                        Gunakan formulir ini <b>hanya</b> jika terjadi pemakaian bahan di luar resep sistem (contoh: bahan tumpah, rusak, atau ekstra tak terduga). Stok gudang akan langsung dipotong dan tercatat pada periode yang sedang berjalan.
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                <form action="{{ route('transactions.storeOut') }}" method="POST" @if($activePeriod) onsubmit="return confirm('Pastikan data dan jumlah barang sudah benar. Lanjutkan pemotongan stok?');" @endif>
                    @csrf

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Periode Aktif</label>
                                @if($activePeriod)
                                    <input type="hidden" name="period_id" value="{{ $activePeriod->id }}">
                                    <div class="w-full rounded-xl border border-slate-200 shadow-inner font-bold text-slate-600 bg-slate-100 px-4 py-2.5 cursor-not-allowed flex items-center gap-2">
                                        <span class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-2 py-0.5 rounded text-[10px] uppercase tracking-widest font-black shadow-sm">Aktif</span>
                                        {{ $activePeriod->name }}
                                    </div>
                                @else
                                    <div class="w-full rounded-xl border border-rose-200 shadow-sm font-bold text-rose-600 bg-rose-50 px-4 py-2.5 cursor-not-allowed flex items-center gap-2">
                                        <span class="text-lg">🔒</span> Tidak ada periode aktif!
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal Keluar</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 shadow-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Pilih Barang (Stok Tersedia)</label>
                                <select name="item_id" class="w-full rounded-xl border-slate-200 shadow-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500 bg-slate-50 cursor-pointer" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }} (Sisa: {{ floatval($item->stock_system) }} {{ $item->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Jumlah / Kuantitas Keluar</label>
                                <input type="number" step="0.01" min="0.01" name="quantity" class="w-full rounded-xl border-slate-200 shadow-sm font-black text-slate-900 focus:ring-rose-500 focus:border-rose-500" placeholder="Contoh: 1.5" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Keterangan / Alasan Keluar</label>
                            <input type="text" name="description" class="w-full rounded-xl border-slate-200 shadow-sm font-medium text-slate-700 focus:ring-rose-500 focus:border-rose-500 bg-slate-50 focus:bg-white transition-colors py-3" placeholder="Contoh: 1 Kg telur pecah saat diangkut" required>
                            <p class="text-[11px] text-gray-400 mt-1.5 font-bold">*Wajib diisi agar pengeluaran dapat dipertanggungjawabkan.</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ url()->previous() }}" class="text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors">Batal & Kembali</a>
                        
                        <button type="submit" 
                                @if(!$activePeriod) disabled @endif
                                class="{{ $activePeriod ? 'bg-rose-600 hover:bg-rose-700' : 'bg-slate-400 cursor-not-allowed opacity-70' }} text-white font-black px-8 py-3 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Potong Stok Sekarang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>