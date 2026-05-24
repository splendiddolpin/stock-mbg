<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-emerald-100 text-emerald-600 p-2 rounded-lg">🛡️</span>
                {{ __('Verifikasi Penerimaan Barang Datang') }}
            </h2>
            <div class="bg-white px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 shadow-sm">
                Periode Aktif: <span class="text-emerald-600">{{ $activePeriod ? $activePeriod->name : 'Tidak Ada' }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-medium flex items-center gap-2">
                    ✅ <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(!$activePeriod)
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm font-medium">
                    ⚠️ Silakan buka Periode baru terlebih dahulu untuk mengelola logistik gudang.
                </div>
            @else
                
                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl p-4">
                    <h3 class="font-bold text-gray-700 mb-3 px-1">Pilih Tanggal Target Masakan:</h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                        @foreach($calendarData as $day)
                            <a href="{{ route('transactions.check-order', ['date' => $day['date']]) }}" 
                               class="block border rounded-xl flex flex-col overflow-hidden bg-white transition-all transform hover:-translate-y-1 hover:shadow-md 
                               {{ $day['is_selected'] ? 'border-emerald-500 ring-2 ring-emerald-200 shadow-md scale-105' : 'border-gray-200' }}">
                                
                                <div class="text-center py-2 {{ $day['is_sunday'] ? 'bg-red-500 text-white' : ($day['is_selected'] ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700 border-b border-gray-200') }}">
                                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-90">{{ $day['day_name'] }}</div>
                                    <div class="text-2xl font-black leading-none my-1">{{ $day['day_num'] }}</div>
                                    <div class="text-[10px] font-semibold opacity-90">{{ $day['month'] }}</div>
                                </div>
                                
                                <div class="p-2 text-center bg-white flex-1 flex flex-col justify-center text-[11px]">
                                    @if($day['is_sunday'])
                                        <span class="text-red-500 font-bold">LIBUR</span>
                                    @elseif($day['total_count'] === 0)
                                        <span class="text-gray-400 font-medium">Belum Di-PO</span>
                                    @elseif($day['pending_count'] > 0)
                                        <span class="bg-orange-50 border border-orange-100 text-orange-700 font-bold px-1.5 py-0.5 rounded animate-pulse">
                                            ⏳ Cek {{ $day['pending_count'] }} Barang
                                        </span>
                                    @else
                                        <span class="bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold px-1.5 py-0.5 rounded">
                                            ✔ Selesai Cek
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($pendingOrders->isEmpty())
                    <div class="bg-gray-50 border border-dashed text-center py-16 rounded-2xl text-gray-500">
                        <span class="text-4xl block mb-2">📭</span>
                        <p class="font-bold text-gray-500 text-lg">Tidak Ada Antrean Barang Datang</p>
                        <p class="text-sm text-gray-400 mt-1">Tidak ada ajuan Surat Pesanan (PO) tertunda, atau semua barang untuk tanggal masakan {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }} telah selesai diverifikasi masuk gudang.</p>
                    </div>
                @else
                    <form action="{{ route('transactions.store-check') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        @csrf
                        <input type="hidden" name="date_of_cooking" value="{{ $selectedDate }}">

                        <div class="p-4 bg-emerald-50/50 border-b border-emerald-100">
                            <h3 class="font-bold text-gray-800 text-base">Verifikasi Fisik Logistik Masakan: <span class="text-emerald-700">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span></h3>
                            <p class="text-xs text-gray-500 mt-1">Ubah angka kolom hijau jika berat timbangan barang asli di pasar berbeda dengan surat pesanan Ahli Gizi.</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-800 text-white text-xs uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="py-3.5 px-4 w-1/3">Nama Bahan Baku / Bumbu</th>
                                        <th class="py-3.5 px-4 text-center">Jumlah Pesanan Ahli Gizi</th>
                                        <th class="py-3.5 px-4 text-center bg-emerald-600/20 border-x border-emerald-500/20">Berat Timbangan Fisik Datang</th>
                                        <th class="py-3.5 px-4 text-center">Indikator Selisih</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-600">
                                    @foreach($pendingOrders as $order)
                                        <tr class="hover:bg-gray-50/50 transition-colors" x-data="{ ordered: {{ $order->qty_ordered }}, received: {{ $order->qty_ordered }} }">
                                            <td class="py-4 px-4 font-bold text-gray-900 text-base">{{ $order->item_name }}</td>
                                            
                                            <td class="py-4 px-4 text-center font-bold text-gray-500">
                                                {{ floatval($order->qty_ordered) }} <span class="text-xs font-normal text-gray-400" uppercase>{{ $order->item_unit }}</span>
                                            </td>
                                            
                                            <td class="py-2 px-4 text-center bg-emerald-50/20 border-x border-emerald-100">
                                                <div class="flex justify-center items-center gap-2">
                                                    <input type="number" step="0.01" min="0" name="items[{{ $order->id }}][qty_received]" x-model.number="received"
                                                           class="w-28 text-center border-emerald-400 bg-emerald-50/30 focus:bg-white rounded-lg font-black text-emerald-800 text-lg focus:ring-emerald-500 shadow-sm transition">
                                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide" x-text="'{{ $order->item_unit }}'"></span>
                                                </div>
                                            </td>

                                            <td class="py-4 px-4 text-center font-bold text-xs">
                                                <span class="px-2 py-1 rounded-md font-black"
                                                      :class="{'text-emerald-700 bg-emerald-100': received == ordered, 'text-amber-700 bg-amber-100': received != ordered}"
                                                      x-text="received == ordered ? '✅ COCOK' : '⚠️ SELISIH (' + (received - ordered).toFixed(2) + ')'">
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Verifikasi Selesai & Masukkan ke Gudang
                            </button>
                        </div>
                    </form>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>