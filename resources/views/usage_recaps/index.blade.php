<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="bg-orange-100 text-orange-600 p-2 rounded-lg">📤</span>
            {{ __('Laporan Barang Keluar (Penggunaan)') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="p-5 bg-gradient-to-r from-slate-800 to-slate-700 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-white font-bold tracking-wider">Histori Pemakaian Dapur</h3>
                    <span class="text-xs bg-white/20 text-white px-3 py-1 rounded-full italic shadow-sm">
                        🤖 Dicatat otomatis oleh sistem sesuai jadwal menu.
                    </span>
                </div>

                <div class="overflow-x-auto p-4">
                    <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="py-4 px-6 text-center">Tanggal Keluar</th>
                                    <th class="py-4 px-6">Nama Bahan Baku</th>
                                    <th class="py-4 px-6">Menu Tujuan</th>
                                    <th class="py-4 px-6 text-center">Jumlah Dipakai</th>
                                    <th class="py-4 px-6 text-right">Nilai Barang (HPP)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recaps as $recap)
                                    <tr class="hover:bg-orange-50/40 transition-colors">
                                        <td class="py-4 px-6 text-center font-bold text-gray-500">
                                            {{ \Carbon\Carbon::parse($recap->date)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="py-4 px-6 font-black text-gray-800 text-base">
                                            {{ $recap->item->name ?? 'Bahan Dihapus' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-md text-xs font-bold uppercase border border-indigo-100 shadow-sm">
                                                {{ $recap->menu->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center font-black text-rose-600 text-base">
                                            -{{ floatval($recap->quantity_out) }} <span class="text-xs font-bold text-rose-400">{{ $recap->unit }}</span>
                                        </td>
                                        <td class="py-4 px-6 text-right font-black text-gray-700">
                                            Rp {{ number_format($recap->total_cost, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center bg-gray-50/50">
                                            <div class="flex flex-col items-center text-gray-400">
                                                <span class="text-6xl mb-4 text-gray-300">📁</span>
                                                <p class="text-xl font-black text-gray-500">Belum ada rekap penggunaan.</p>
                                                <p class="text-sm mt-1">Robot akan mencatat rekap saat jadwal menu dieksekusi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($recaps->hasPages())
                    <div class="p-4 bg-gray-50 border-t border-gray-100">
                        {{ $recaps->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>