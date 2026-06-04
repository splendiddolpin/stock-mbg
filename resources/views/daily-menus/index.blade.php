<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg shadow-sm">🗓️</span>
                {{ __('Atur Jadwal Menu Mingguan') }}
            </h2>
            <div class="bg-white px-4 py-2 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 shadow-sm flex items-center gap-2">
                Periode Aktif: 
                <span class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded uppercase tracking-wider">
                    {{ $activePeriod ? $activePeriod->name : 'TIDAK ADA' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm relative animate-fade-in-down">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(!$activePeriod)
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-center gap-4">
                    <span class="text-5xl drop-shadow-sm">🔒</span>
                    <div>
                        <strong class="block text-xl font-black tracking-wider uppercase mb-1">Sistem Terkunci!</strong>
                        <p class="font-medium">Tidak ada <b>Buku Periode</b> yang aktif saat ini. Silakan minta Asisten Lapangan (Aslap) untuk membuka periode baru agar sistem penjadwalan dapur terbuka.</p>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-l-4 border-blue-500 relative">
                    <div class="absolute top-0 right-0 bg-blue-50 text-blue-600 text-[10px] font-black px-4 py-1.5 rounded-bl-xl border-b border-l border-blue-100 uppercase tracking-widest">
                        Batas Input: {{ date('d M Y', strtotime($activePeriod->start_date)) }} s/d {{ date('d M Y', strtotime($activePeriod->end_date)) }}
                    </div>

                    <h3 class="font-black text-gray-800 text-lg mb-4 mt-2">Tambah Jadwal Masak</h3>

                    <form action="{{ route('daily-menus.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                        @csrf
                        
                        <div class="w-full">
                            <label class="block text-xs font-bold mb-1.5 text-gray-500 uppercase tracking-wider">📅 Tanggal Sajian</label>
                            @php
                                $hariIni = date('Y-m-d');
                                $defaultDate = ($hariIni >= $activePeriod->start_date && $hariIni <= $activePeriod->end_date) ? $hariIni : $activePeriod->start_date;
                            @endphp
                            <input type="date" name="date" 
                                   min="{{ $activePeriod->start_date }}" 
                                   max="{{ $activePeriod->end_date }}" 
                                   value="{{ $defaultDate }}" 
                                   class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-bold text-blue-700 bg-blue-50/50 transition-all" required>
                        </div>

                        <div class="w-full">
                            <label class="block text-xs font-bold mb-1.5 text-gray-500 uppercase tracking-wider">🍽️ Pilih Menu</label>
                            <select name="menu_id" class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-medium cursor-pointer" required>
                                <option value="" class="text-gray-400">-- Pilih Menu Master --</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full">
                            <label class="block text-xs font-bold mb-1.5 text-gray-500 uppercase tracking-wider">🎯 Target Penerima</label>
                            <select name="target_type" class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 font-medium cursor-pointer" required>
                                <option value="semua">Semua (Sekolah & Posyandu)</option>
                                <option value="sekolah">Khusus Sekolah (Ompreng)</option>
                                <option value="posyandu">Khusus Posyandu (Rapelan)</option>
                            </select>
                        </div>

                        <div class="w-full">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 px-6 rounded-xl shadow-md shadow-blue-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                    <p class="mt-5 text-xs text-gray-400 font-bold bg-gray-50 p-2 rounded-lg border border-gray-100 w-fit">
                        💡 Info: Ahli Gizi hanya bisa menjadwalkan menu pada rentang tanggal periode yang sedang aktif.
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                    
                    <div class="p-5 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">📋</div>
                            <div>
                                <h3 class="font-black text-gray-800 text-lg">Daftar Jadwal Masak</h3>
                                <p class="text-xs font-bold text-gray-500">Terdapat <span class="text-blue-600">{{ $schedules->count() }} jadwal</span> terdaftar pada periode ini.</p>
                            </div>
                        </div>
                        
                        @if($schedules->count() > 0)
                            <form action="{{ route('daily-menus.destroy-all') }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS! 🚨 Apakah Anda yakin ingin menghapus SEMUA jadwal masak di periode aktif ini? Tindakan ini tidak bisa dibatalkan!');">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="text-xs bg-white hover:bg-rose-600 text-rose-600 hover:text-white border-2 border-rose-200 hover:border-rose-600 font-black py-2 px-4 rounded-xl transition-all flex items-center gap-2 shadow-sm group">
                                    <svg class="w-4 h-4 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus Semua Jadwal
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="overflow-x-auto p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-800 text-white">
                                <tr>
                                    <th class="py-4 px-5 font-bold uppercase tracking-wider text-xs">Tanggal</th>
                                    <th class="py-4 px-5 font-bold uppercase tracking-wider text-xs">Nama Menu Sajian</th>
                                    <th class="py-4 px-5 font-bold uppercase tracking-wider text-xs text-center">Target Penerima</th>
                                    <th class="py-4 px-5 font-bold uppercase tracking-wider text-xs text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                @forelse($schedules as $schedule)
                                    <tr class="hover:bg-blue-50/40 transition-colors group">
                                        <td class="py-4 px-5">
                                            <div class="font-bold {{ $schedule->date == date('Y-m-d') ? 'text-blue-600' : 'text-gray-800' }}">
                                                {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('l, d M Y') }}
                                            </div>
                                            @if($schedule->date == date('Y-m-d'))
                                                <span class="inline-block text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full mt-1 font-black tracking-widest border border-blue-200">HARI INI</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="font-black text-gray-900 text-base group-hover:text-blue-700 transition-colors">
                                                {{ $schedule->menu->name }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($schedule->target_type === 'semua')
                                                <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs font-black tracking-widest uppercase border border-indigo-100 shadow-sm">Semua</span>
                                            @elseif($schedule->target_type === 'sekolah')
                                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-black tracking-widest uppercase border border-blue-100 shadow-sm">Sekolah</span>
                                            @else
                                                <span class="bg-pink-50 text-pink-700 px-3 py-1 rounded-lg text-xs font-black tracking-widest uppercase border border-pink-100 shadow-sm">Posyandu</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            <form action="{{ route('daily-menus.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal menu masak ini?');">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-rose-600 bg-gray-50 hover:bg-rose-50 p-2 rounded-lg transition-all border border-transparent hover:border-rose-200" title="Hapus Jadwal">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-16 text-center bg-gray-50/50">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="bg-white p-4 rounded-full shadow-sm border border-gray-100">
                                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                                                </div>
                                                <div>
                                                    <p class="font-black text-gray-500">Belum Ada Jadwal</p>
                                                    <p class="text-xs font-medium text-gray-400 mt-1">Gunakan form di atas untuk mulai menyusun menu harian.</p>
                                                </div>
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