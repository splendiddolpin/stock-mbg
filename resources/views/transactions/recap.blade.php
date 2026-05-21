<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekap Barang Masuk (Per Periode)') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @forelse($periods as $period)
        @php
            // Mengelompokkan transaksi milik periode ini berdasarkan tanggal
            $groupedByDate = $period->transactions->groupBy('date');
        @endphp
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-blue-600 text-white font-bold flex justify-between items-center">
                    <span class="text-lg">{{ $period->name }}</span>
                    <span class="text-sm font-normal bg-blue-800 px-3 py-1 rounded-full">{{ $period->transactions->count() }} Transaksi</span>
                </div>
                
                <div class="p-6">
                    @foreach($groupedByDate as $date => $items)
                        <div class="mb-6 last:mb-0">
                            <h4 class="font-bold text-gray-700 border-b-2 border-gray-200 mb-3 pb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                            </h4>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-600">
                                        <tr>
                                            <th class="py-2 px-4 w-12 text-center">No</th>
                                            <th class="py-2 px-4">Nama Barang</th>
                                            <th class="py-2 px-4 text-center">Jumlah Masuk</th>
                                            <th class="py-2 px-4">Keterangan / Supplier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $idx => $trx)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="py-2 px-4 text-center">{{ $loop->iteration }}</td>
                                                <td class="py-2 px-4 font-medium text-gray-900">{{ $trx->item->name }}</td>
                                                <td class="py-2 px-4 text-center text-green-600 font-bold">+{{ $trx->quantity }} {{ $trx->item->unit }}</td>
                                                <td class="py-2 px-4 text-gray-500">{{ $trx->description ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                Belum ada data barang masuk yang dicatat di gudang.
            </div>
        @endforelse
    </div>
</x-app-layout>