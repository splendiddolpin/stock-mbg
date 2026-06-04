<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">📥</span>
            {{ __('Rekap Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @forelse($periods as $period)
                @php
                    $groupedByDate = $period->transactions->groupBy('date');
                @endphp
                
                <div x-data="{ open: true }" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-blue-100">
                    <button @click="open = !open" class="w-full p-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold flex justify-between items-center focus:outline-none rounded-t-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-xl bg-white/20 p-1.5 rounded-lg">🗓️</span>
                            <span class="text-lg tracking-wider">{{ $period->name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black bg-white text-blue-700 px-3 py-1 rounded-full shadow-sm">{{ $period->transactions->count() }} Transaksi</span>
                            <svg :class="{'rotate-180': open}" class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    
                    <div x-show="open" x-collapse>
                        <div class="p-6 bg-gray-50/30">
                            @forelse($groupedByDate as $date => $items)
                                <div class="mb-8 last:mb-0 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                                    <h4 class="font-black text-gray-800 border-b-2 border-dashed border-gray-200 mb-4 pb-3 flex items-center text-base">
                                        <span class="bg-gray-100 p-1.5 rounded-md mr-3">📅</span>
                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                    </h4>
                                    
                                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                                        <table class="min-w-full text-sm text-left">
                                            <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                                                <tr>
                                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                                    <th class="py-3 px-4">Nama Barang</th>
                                                    <th class="py-3 px-4 text-center">Jumlah Masuk</th>
                                                    <th class="py-3 px-4">Keterangan / Supplier</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($items as $idx => $trx)
                                                    <tr class="hover:bg-blue-50/50 transition-colors">
                                                        <td class="py-3 px-4 text-center font-bold text-gray-400">{{ $loop->iteration }}</td>
                                                        <td class="py-3 px-4 font-black text-gray-900">{{ $trx->item->name ?? 'Bahan Dihapus' }}</td>
                                                        <td class="py-3 px-4 text-center text-emerald-600 font-black text-base">+{{ $trx->quantity }} <span class="text-xs font-bold">{{ $trx->item->unit ?? '' }}</span></td>
                                                        <td class="py-3 px-4 text-gray-500 font-medium italic">{{ $trx->description ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-400 italic">Belum ada transaksi di periode ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-2xl border border-dashed border-gray-300 p-16 text-center">
                    <span class="text-6xl mb-4 block">📭</span>
                    <h3 class="text-xl font-black text-gray-700">Gudang Masih Kosong</h3>
                    <p class="text-gray-500 mt-2">Belum ada data barang masuk yang dicatat oleh tim Logistik.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>