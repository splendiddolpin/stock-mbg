<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="bg-orange-100 text-orange-600 p-2 rounded-lg">📊</span>
            {{ __('Laporan Rekap Penggunaan Bahan Baku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                    <p class="text-sm text-gray-600 italic">Catatan: Data ini dibuat otomatis oleh sistem setiap jam 12:00 siang berdasarkan jadwal menu harian.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                            <tr>
                                <th class="py-4 px-6 text-center">Tanggal Keluar</th>
                                <th class="py-4 px-6">Nama Bahan</th>
                                <th class="py-4 px-6">Menu Terkait</th>
                                <th class="py-4 px-6 text-center">Jumlah Keluar</th>
                                <th class="py-4 px-6 text-right">Nilai Barang (HPP)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recaps as $recap)
                                <tr class="hover:bg-orange-50/30 transition-colors">
                                    <td class="py-4 px-6 text-center font-medium text-gray-500">
                                        {{ date('d/m/Y', strtotime($recap->date)) }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-800">
                                        {{ $recap->item->name }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold uppercase border border-blue-100">
                                            {{ $recap->menu->name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center font-black text-orange-600">
                                        {{ floatval($recap->quantity_out) }} {{ $recap->unit }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-bold text-emerald-600">
                                        Rp {{ number_format($recap->total_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <span class="text-5xl mb-4 text-gray-300">📁</span>
                                            <p class="text-lg font-medium">Belum ada rekap penggunaan hari ini.</p>
                                            <p class="text-sm">Robot akan mencatat rekap saat jadwal menu dijalankan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    {{ $recaps->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>