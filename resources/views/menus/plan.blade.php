<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">📊</span>
                {{ __('Estimasi Belanja Menu: ') }} <span class="text-blue-600 font-black">{{ $menu->name }}</span>
            </h2>
            <a href="{{ route('menus.show', $menu->id) }}" class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 border border-gray-200 px-4 py-2 rounded-lg text-sm font-bold transition">Kembali ke Resep</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Target Penerima Manfaat (Asumsi Master Data)</h3>
                    <p class="text-sm text-gray-500">Kalkulasi di bawah ini dihitung berdasarkan total seluruh penerima manfaat yang aktif saat ini.</p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-blue-50 border border-blue-100 px-4 py-2 rounded-lg text-center shadow-sm">
                        <div class="text-xs font-bold text-blue-600 uppercase">Porsi Besar</div>
                        <div class="text-xl font-black text-blue-800">{{ number_format($totalPorsiBesar + $totalBumilBusui) }} <span class="text-sm font-medium text-blue-500">Jiwa</span></div>
                        <div class="text-[10px] text-gray-500 mt-1">Siswa: {{ $totalPorsiBesar }} | Bumil: {{ $totalBumilBusui }}</div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-lg text-center shadow-sm">
                        <div class="text-xs font-bold text-emerald-600 uppercase">Porsi Kecil</div>
                        <div class="text-xl font-black text-emerald-800">{{ number_format($totalPorsiKecil + $totalBalita) }} <span class="text-sm font-medium text-emerald-500">Jiwa</span></div>
                        <div class="text-[10px] text-gray-500 mt-1">Siswa: {{ $totalPorsiKecil }} | Balita: {{ $totalBalita }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Rincian Kebutuhan Bahan vs Stok Gudang</h3>
                </div>
                
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-800 text-white">
                            <tr>
                                <th class="py-3 px-4">Nama Bahan</th>
                                <th class="py-3 px-4 text-center">Total Kebutuhan</th>
                                <th class="py-3 px-4 text-center">Stok Gudang</th>
                                <th class="py-3 px-4 text-center">Status Sisa Stok</th>
                                <th class="py-3 px-4 text-right">Estimasi Biaya Beli</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @php $totalBiayaKeseluruhan = 0; @endphp
                            
                            @forelse($kalkulasiKebutuhan as $kalkulasi)
                                @php $totalBiayaKeseluruhan += $kalkulasi['biaya']; @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4 font-bold text-gray-800 text-base">{{ $kalkulasi['nama_bahan'] }}</td>
                                    
                                    <td class="py-4 px-4 text-center font-black text-indigo-600">
                                        {{ floatval($kalkulasi['total_kebutuhan']) }} <span class="text-xs font-normal text-gray-500">{{ $kalkulasi['satuan'] }}</span>
                                    </td>
                                    
                                    <td class="py-4 px-4 text-center font-bold text-gray-600">
                                        {{ floatval($kalkulasi['stok_gudang']) }} <span class="text-xs font-normal text-gray-400">{{ $kalkulasi['satuan'] }}</span>
                                    </td>
                                    
                                    <td class="py-4 px-4 text-center">
                                        @if($kalkulasi['defisit'] > 0)
                                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1.5 rounded-lg font-bold border border-red-200">
                                                ⚠️ Kurang {{ floatval($kalkulasi['defisit']) }} {{ $kalkulasi['satuan'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg font-bold border border-emerald-200">
                                                ✅ Aman (Sisa {{ floatval($kalkulasi['sisa_stok']) }})
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-4 px-4 text-right font-bold {{ $kalkulasi['biaya'] > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                        Rp {{ number_format($kalkulasi['biaya'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            <p class="font-medium">Resep masih kosong. Silakan tambahkan bahan terlebih dahulu.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-indigo-50 border-t-2 border-indigo-100">
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-right font-bold text-gray-700 text-base">Total Estimasi Anggaran Diperlukan:</td>
                                <td class="py-4 px-4 text-right font-black text-red-600 text-lg">
                                    Rp {{ number_format($totalBiayaKeseluruhan, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>