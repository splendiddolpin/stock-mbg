<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <div class="bg-amber-100 text-amber-600 p-2.5 rounded-xl shadow-sm">💰</div>
            {{ __('Buku Kas Staf & Utang') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm font-bold flex items-center gap-3 animate-fade-in-down">
                    <span class="text-xl">✅</span> <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-700 p-6 rounded-3xl shadow-lg text-white">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <p class="text-emerald-100 text-xs font-black tracking-widest uppercase mb-1">Saldo Kas Aktif</p>
                        <h3 class="text-4xl font-black tracking-tight">Rp {{ number_format($saldo, 0, ',', '.') }}</h3>
                        <p class="mt-4 text-sm font-medium text-emerald-50 opacity-90 flex items-center gap-2">
                            <span>📈</span> Uang tunai murni yang tersedia saat ini.
                        </p>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-rose-500 to-red-700 p-6 rounded-3xl shadow-lg text-white">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <p class="text-rose-100 text-xs font-black tracking-widest uppercase mb-1">Total Dana Keluar (Utang)</p>
                        <h3 class="text-4xl font-black tracking-tight">Rp {{ number_format($totalUtang ?? 0, 0, ',', '.') }}</h3>
                        <p class="mt-4 text-sm font-medium text-rose-50 opacity-90 flex items-center gap-2">
                            <span>⏳</span> Menunggu reimbursement/pengembalian.
                        </p>
                    </div>
                </div>
            </div>

            <div x-data="{ type: 'out' }" class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                    <span class="bg-amber-50 text-amber-600 p-2.5 rounded-xl text-xl">📝</span>
                    <div>
                        <h3 class="font-black text-gray-800 text-lg">Catat Transaksi Baru</h3>
                        <p class="text-xs font-bold text-gray-400 mt-0.5">Isi detail pemasukan atau pengeluaran kas di bawah ini.</p>
                    </div>
                </div>
                
                <form action="{{ route('staff-cash.store') }}" method="POST">
                    @csrf
                    
                    <div class="bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-100 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tipe Transaksi</label>
                                <select name="type" x-model="type" class="w-full rounded-xl border-slate-200 shadow-sm font-black text-slate-700 focus:ring-amber-500 focus:border-amber-500 cursor-pointer bg-white" required>
                                    <option value="out">Keluar 📉 (Pengeluaran)</option>
                                    <option value="in">Masuk 📈 (Pemasukan)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal Transaksi</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 shadow-sm font-black text-slate-700 focus:ring-amber-500 focus:border-amber-500 bg-white" required>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Kategori</label>
                                
                                <select name="category" x-show="type === 'out'" x-bind:disabled="type === 'in'" class="w-full rounded-xl border-rose-200 shadow-sm font-black focus:ring-rose-500 focus:border-rose-500 text-rose-700 cursor-pointer bg-rose-50">
                                    <option value="kas" class="text-slate-700">Murni Kas (Bukan Utang)</option>
                                    <option value="mitra">Mitra (Kas Bon)</option>
                                    <option value="operasional">Operasional (Kas Bon)</option>
                                    <option value="bahan_makanan">Bahan Makanan (Kas Bon)</option>
                                </select>

                                <select name="category" x-show="type === 'in'" x-bind:disabled="type === 'out'" style="display: none;" class="w-full rounded-xl border-emerald-200 shadow-sm font-black focus:ring-emerald-500 focus:border-emerald-500 text-emerald-700 cursor-pointer bg-emerald-50">
                                    <option value="kas">Pemasukan / Saldo Awal</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Nominal Uang</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-black text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="amount" min="1" class="w-full pl-11 rounded-xl border-slate-200 shadow-sm font-black text-slate-900 focus:ring-amber-500 focus:border-amber-500 bg-white" placeholder="150000" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-5 items-end">
                        <div class="flex-grow w-full">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Keterangan / Rincian</label>
                            <input type="text" name="description" class="w-full rounded-xl border-slate-200 shadow-sm font-medium text-slate-700 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 focus:bg-white transition-colors py-3" placeholder="Cth: Pembelian bumbu dapur / Talangan gas LPG" required>
                        </div>
                        
                        <button type="submit" class="w-full lg:w-auto bg-amber-500 hover:bg-amber-600 text-white font-black px-8 py-3 rounded-xl shadow-md transition-all active:scale-95 shrink-0 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            <span>Simpan Catatan</span>
                        </button>
                    </div>
                </form>
            </div>

            <div x-data="{ open: false }" class="bg-slate-800 rounded-3xl shadow-md border border-slate-700 overflow-hidden">
                <button @click="open = !open" class="w-full p-5 sm:px-8 bg-slate-800 hover:bg-slate-700 flex flex-col sm:flex-row sm:items-center justify-between transition-colors focus:outline-none gap-4 group">
                    <div class="flex items-center gap-4">
                        <span class="bg-slate-700 text-white p-2.5 rounded-xl shadow-inner text-xl border border-slate-600 group-hover:scale-110 transition-transform">📋</span>
                        <div class="text-left">
                            <h3 class="font-black text-white text-lg tracking-wide uppercase">Rekapan Semua Transaksi</h3>
                            <p class="text-xs font-bold text-slate-400 mt-0.5">Melihat seluruh histori secara kronologis (Semua Kategori)</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 justify-start sm:justify-end">
                        <span class="text-xs font-black bg-slate-700 text-slate-300 border border-slate-600 px-4 py-1.5 rounded-full shadow-sm whitespace-nowrap">
                            {{ $cashes->count() }} Total Data
                        </span>
                        <svg :class="{'rotate-180': open}" class="w-6 h-6 text-slate-400 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </button>
                
                <div x-show="open" x-transition.opacity x-collapse>
                    <div class="overflow-x-auto bg-white rounded-b-3xl">
                        <table class="w-full text-sm text-left border-t border-slate-700">
                            <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="py-4 px-6 text-center">Tanggal</th>
                                    <th class="py-4 px-6 text-center">Kategori</th>
                                    <th class="py-4 px-6">Keterangan Transaksi</th>
                                    <th class="py-4 px-6 text-right">Masuk (Rp)</th>
                                    <th class="py-4 px-6 text-right">Keluar (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($cashes as $cash)
                                    <tr class="hover:bg-slate-50 transition-colors {{ $cash->is_debt && !$cash->is_paid ? 'bg-rose-50/40' : '' }}">
                                        <td class="py-4 px-6 text-center font-bold text-gray-500 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($cash->date)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            @if($cash->category == 'kas')
                                                <span class="bg-gray-100 border border-gray-200 text-gray-600 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest shadow-sm whitespace-nowrap">Kas</span>
                                            @elseif($cash->category == 'mitra')
                                                <span class="bg-purple-50 border border-purple-200 text-purple-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest shadow-sm whitespace-nowrap">Mitra</span>
                                            @elseif($cash->category == 'operasional')
                                                <span class="bg-orange-50 border border-orange-200 text-orange-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest shadow-sm whitespace-nowrap">Operasional</span>
                                            @elseif($cash->category == 'bahan_makanan')
                                                <span class="bg-teal-50 border border-teal-200 text-teal-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest shadow-sm whitespace-nowrap">Bahan Makanan</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-black text-gray-800 text-base">
                                            {{ $cash->description }}
                                            @if($cash->is_debt && !$cash->is_paid)
                                                <span class="ml-2 inline-block text-[10px] bg-rose-100 text-rose-600 px-2 py-0.5 rounded-full font-bold whitespace-nowrap">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right font-black text-emerald-600 text-base whitespace-nowrap">
                                            {{ $cash->type === 'in' ? number_format($cash->amount, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-4 px-6 text-right font-black text-rose-600 text-base whitespace-nowrap">
                                            {{ $cash->type === 'out' ? number_format($cash->amount, 0, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-gray-400 font-bold">Belum ada riwayat transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            @if($cashes->count() > 0)
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                                <tr>
                                    <td colspan="3" class="py-4 px-6 text-right font-black text-slate-600 uppercase tracking-wider text-xs">
                                        Total Keseluruhan
                                    </td>
                                    <td class="py-4 px-6 text-right font-black text-emerald-600 text-base whitespace-nowrap">
                                        Rp {{ number_format($cashes->where('type', 'in')->sum('amount'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-black text-rose-600 text-base whitespace-nowrap">
                                        Rp {{ number_format($cashes->where('type', 'out')->sum('amount'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 border-l-4 border-amber-500 pl-4">
                        <span class="text-2xl">🗂️</span>
                        <h3 class="font-black text-gray-800 text-xl tracking-wide">Riwayat Pengeluaran Per Kategori</h3>
                    </div>
                </div>

                @php
                    $groupedCashes = $cashes->where('category', '!=', 'kas')->groupBy('category');
                    
                    $catStyles = [
                        'mitra' => ['name' => 'Pengeluaran Mitra', 'icon' => '🤝', 'bg' => 'bg-purple-50', 'text' => 'text-purple-800', 'border' => 'border-purple-200', 'badge' => 'text-purple-700 bg-purple-200'],
                        'operasional' => ['name' => 'Pengeluaran Operasional', 'icon' => '🏢', 'bg' => 'bg-orange-50', 'text' => 'text-orange-800', 'border' => 'border-orange-200', 'badge' => 'text-orange-700 bg-orange-200'],
                        'bahan_makanan' => ['name' => 'Belanja Bahan Makanan', 'icon' => '🥬', 'bg' => 'bg-teal-50', 'text' => 'text-teal-800', 'border' => 'border-teal-200', 'badge' => 'text-teal-700 bg-teal-200'],
                    ];
                @endphp

                <div class="space-y-6">
                    @forelse($groupedCashes as $category => $items)
                        @php 
                            $style = $catStyles[$category]; 
                            $unpaidCount = $items->where('is_debt', true)->where('is_paid', false)->count();
                            $unpaidAmount = $items->where('is_debt', true)->where('is_paid', false)->sum('amount');
                        @endphp
                        
                        <div x-data="{ open: true }" class="bg-white rounded-3xl shadow-sm border {{ $style['border'] }} overflow-hidden">
                            <button @click="open = !open" class="w-full p-4 sm:p-5 {{ $style['bg'] }} border-b {{ $style['border'] }} flex flex-col sm:flex-row sm:items-center justify-between transition-colors focus:outline-none gap-4">
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="bg-white p-2.5 rounded-xl shadow-sm text-xl border border-white/50">{{ $style['icon'] }}</span>
                                    <div class="text-left">
                                        <h3 class="font-black {{ $style['text'] }} text-base md:text-lg tracking-wide uppercase leading-tight">{{ $style['name'] }}</h3>
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-2 justify-start sm:justify-end shrink-0">
                                    
                                    @if($unpaidCount > 0)
                                        <span class="text-xs font-black bg-rose-100 text-rose-700 px-3 py-1.5 rounded-full shadow-sm border border-rose-200 flex items-center gap-1 animate-pulse whitespace-nowrap">
                                            ⏳ {{ $unpaidCount }} Utang (Rp {{ number_format($unpaidAmount, 0, ',', '.') }})
                                        </span>
                                    @endif

                                    <span class="text-xs font-black {{ $style['badge'] }} px-3 py-1.5 rounded-full shadow-sm whitespace-nowrap">
                                        {{ $items->count() }} Transaksi
                                    </span>
                                    
                                    <svg :class="{'rotate-180': open}" class="w-5 h-5 {{ $style['text'] }} transform transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            
                            <div x-show="open" x-transition.opacity>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                                            <tr>
                                                <th class="py-4 px-6 text-center">Tanggal</th>
                                                <th class="py-4 px-6">Keterangan Transaksi</th>
                                                <th class="py-4 px-6 text-right">Masuk (Rp)</th>
                                                <th class="py-4 px-6 text-right">Keluar (Rp)</th>
                                                <th class="py-4 px-6 text-center">Status / Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($items as $cash)
                                                <tr class="hover:bg-gray-50/80 transition-colors {{ $cash->is_debt && !$cash->is_paid ? 'bg-rose-50/40' : '' }}">
                                                    <td class="py-4 px-6 text-center font-bold text-gray-500 whitespace-nowrap">
                                                        {{ \Carbon\Carbon::parse($cash->date)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="py-4 px-6 font-black text-gray-800 text-base">
                                                        {{ $cash->description }}
                                                    </td>
                                                    <td class="py-4 px-6 text-right font-black text-emerald-600 text-base whitespace-nowrap">
                                                        {{ $cash->type === 'in' ? number_format($cash->amount, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="py-4 px-6 text-right font-black text-rose-600 text-base whitespace-nowrap">
                                                        {{ $cash->type === 'out' ? number_format($cash->amount, 0, ',', '.') : '-' }}
                                                    </td>
                                                    <td class="py-4 px-6">
                                                        <div class="flex items-center justify-center gap-3">
                                                            
                                                            @if($cash->is_debt)
                                                                @if($cash->is_paid)
                                                                    <span class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-1.5 rounded-xl text-xs font-black shadow-sm whitespace-nowrap">
                                                                        ✅ Lunas
                                                                    </span>
                                                                @else
                                                                    <form action="{{ route('staff-cash.pay', $cash->id) }}" method="POST" onsubmit="return confirm('Tandai utang ini sebagai LUNAS? Uang akan otomatis masuk kembali ke Saldo Kas.');">
                                                                        @csrf
                                                                        <button type="submit" class="bg-white hover:bg-emerald-500 text-rose-600 hover:text-white border-2 border-rose-200 hover:border-emerald-500 px-4 py-1.5 rounded-xl text-xs font-black flex items-center gap-1 transition-all group shadow-sm whitespace-nowrap">
                                                                            <span class="group-hover:hidden">⏳ Belum Lunas</span>
                                                                            <span class="hidden group-hover:inline">✔ Centang Lunas</span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @else
                                                                <span class="text-gray-300 font-black text-xs px-4">---</span>
                                                            @endif

                                                            <form action="{{ route('staff-cash.destroy', $cash->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat kas ini secara permanen?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-gray-400 hover:text-rose-600 bg-gray-50 hover:bg-rose-50 p-2 rounded-xl transition-all border border-transparent hover:border-rose-200">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                </button>
                                                            </form>
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="bg-gray-50 p-6 rounded-full shadow-sm border border-gray-100 mb-2">
                                    <span class="text-5xl">💸</span>
                                </div>
                                <div>
                                    <p class="font-black text-gray-800 text-2xl mb-1">Pengeluaran Kosong</p>
                                    <p class="text-sm font-medium text-gray-500">Belum ada riwayat kas bon/pengeluaran untuk kategori-kategori ini.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>