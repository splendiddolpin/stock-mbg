<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Atur Jadwal Menu Mingguan') }}
            </h2>
            <div class="bg-white px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 shadow-sm">
                Periode Aktif: <span class="text-orange-600">{{ $activePeriod ? $activePeriod->name : 'Tidak Ada' }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(!$activePeriod)
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-5 rounded-xl shadow-sm font-medium flex items-center gap-3">
                    <span class="text-3xl">⚠️</span>
                    <div>
                        <strong class="block text-lg">Sistem Terkunci!</strong>
                        Tidak ada Periode Aktif saat ini. Silakan minta Asisten Lapangan (Aslap) untuk membuka Buku Periode baru terlebih dahulu sebelum Anda bisa menyusun jadwal masak.
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500 relative">
                    <div class="absolute top-0 right-0 bg-blue-100 text-blue-700 text-[10px] font-bold px-3 py-1 rounded-bl-lg border-b border-l border-blue-200">
                        Batas Input: {{ date('d M Y', strtotime($activePeriod->start_date)) }} s/d {{ date('d M Y', strtotime($activePeriod->end_date)) }}
                    </div>

                    <form action="{{ route('daily-menus.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mt-2">
                        @csrf
                        
                        <div class="w-full">
                            <label class="block text-sm font-bold mb-2 text-gray-700">📅 Tanggal Sajian</label>
                            @php
                                // Set default value: Jika hari ini di luar periode, set default ke hari pertama periode
                                $hariIni = date('Y-m-d');
                                $defaultDate = ($hariIni >= $activePeriod->start_date && $hariIni <= $activePeriod->end_date) ? $hariIni : $activePeriod->start_date;
                            @endphp
                            <input type="date" name="date" 
                                   min="{{ $activePeriod->start_date }}" 
                                   max="{{ $activePeriod->end_date }}" 
                                   value="{{ $defaultDate }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-bold text-blue-700 bg-blue-50/30" required>
                        </div>

                        <div class="w-full">
                            <label class="block text-sm font-bold mb-2 text-gray-700">🍽️ Pilih Menu</label>
                            <select name="menu_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Menu Master --</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full">
                            <label class="block text-sm font-bold mb-2 text-gray-700">🎯 Target Penerima</label>
                            <select name="target_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="semua">Semua (Sekolah & Posyandu)</option>
                                <option value="sekolah">Khusus Sekolah (Ompreng)</option>
                                <option value="posyandu">Khusus Posyandu (Rapelan)</option>
                            </select>
                        </div>

                        <div class="w-full">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-md transition-all active:scale-95">
                                ➕ Simpan Jadwal
                            </button>
                        </div>
                    </form>
                    <p class="mt-4 text-xs text-gray-500 italic font-medium">* Ahli Gizi hanya bisa menjadwalkan menu pada rentang tanggal periode yang sedang aktif.</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700 flex items-center gap-2">
                            <span>📅</span> Daftar Jadwal Masak Periode Ini
                        </h3>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded-md font-bold text-gray-600">Total: {{ $schedules->count() }} Jadwal</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-800 text-white">
                                <tr>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Menu</th>
                                    <th class="py-3 px-4 text-center">Target Penerima</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                @forelse($schedules as $schedule)
                                    <tr class="hover:bg-blue-50/50 transition-colors">
                                        <td class="py-4 px-4 font-bold {{ $schedule->date == date('Y-m-d') ? 'text-blue-600' : 'text-gray-800' }}">
                                            {{ date('d M Y', strtotime($schedule->date)) }}
                                            @if($schedule->date == date('Y-m-d'))
                                                <span class="block text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full w-fit mt-1">HARI INI</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 font-black text-gray-900 text-base">
                                            {{ $schedule->menu->name }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            @if($schedule->target_type === 'semua')
                                                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold border border-indigo-200">Semua</span>
                                            @elseif($schedule->target_type === 'sekolah')
                                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">Sekolah</span>
                                            @else
                                                <span class="bg-pink-100 text-pink-700 px-3 py-1 rounded-full text-xs font-bold border border-pink-200">Posyandu</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <form action="{{ route('daily-menus.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-4 py-1.5 rounded-lg transition-all font-bold border border-red-200 shadow-sm">
                                                    🗑️ Batal
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-gray-500 bg-gray-50/50">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                                                <p class="font-bold text-gray-400">Belum ada jadwal menu yang dibuat di periode ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>