@php
    // --- KITA BERSIHKAN BLOK INI ---
    // Semua hitungan belanja besok sudah dikerjakan di DashboardController.
    // Di sini kita cuma perlu panggil data Menu TERBARU (Top 5) untuk kotak bawah,
    // dan menggabungkan target porsi untuk estimasi resep.

    $menusWithItems  = \App\Models\Menu::with('items')->latest()->take(5)->get();

    // Gabungkan Target Sesuai Kesepakatan Ahli Gizi:
    // Porsi Besar = Sekolah Besar + Bumil
    // Porsi Kecil = Sekolah Kecil + Balita
    $masterPorsiBesar = $beneficiaries->where('type', 'sekolah')->sum('porsi_besar') + $beneficiaries->where('type', 'posyandu')->sum('total_bumil_busui');
    $masterPorsiKecil = $beneficiaries->where('type', 'sekolah')->sum('porsi_kecil') + $beneficiaries->where('type', 'posyandu')->sum('total_balita');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama MBG') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-blue-500 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div class="text-sm font-bold text-gray-400 uppercase tracking-wider">Penerima Manfaat</div>
                <div class="mt-2">
                    <div class="text-3xl font-black text-gray-900">
                        {{ number_format($totalStudents + $totalJiwaPosyandu) }} <span class="text-sm font-bold text-gray-400">Jiwa</span>
                    </div>
                    <div class="text-xs text-gray-500 font-medium mt-2 flex flex-col gap-1">
                        <span class="text-blue-600 font-bold bg-blue-50 w-fit px-2 py-0.5 rounded">• {{ number_format($totalStudents) }} Siswa <span class="font-normal text-blue-400">({{ $totalSchools }} Sekolah)</span></span>
                        <span class="text-pink-600 font-bold bg-pink-50 w-fit px-2 py-0.5 rounded">• {{ number_format($totalJiwaPosyandu) }} Orang <span class="font-normal text-pink-400">({{ $totalPosyandu }} Posyandu)</span></span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-emerald-500 p-6 hover:shadow-md transition">
                <div class="text-sm font-bold text-gray-400 uppercase tracking-wider">Total Jenis Bahan</div>
                <div class="text-3xl font-black text-gray-900 mt-2">{{ $items->count() }} <span class="text-sm font-bold text-gray-400">Item Gudang</span></div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-l-4 border-red-500 p-6 hover:shadow-md transition">
                <div class="text-sm font-bold text-gray-400 uppercase tracking-wider">Peringatan Stok</div>
                <div class="text-3xl font-black text-red-600 mt-2">{{ $lowStockCount }} <span class="text-sm font-bold text-red-400">Kritis</span></div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border-t-4 border-indigo-500 mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-xl font-black text-gray-800">🍳 Menu Utama Hari Ini</h3>
                        <p class="text-sm font-bold text-indigo-500 mt-1">Tanggal: {{ now()->translatedFormat('d F Y') }}</p>
                    </div>

                    @if($jadwalHariIni)
                        <div class="flex items-center gap-4 bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                            <div class="text-right">
                                <span class="block font-black text-indigo-700 uppercase text-lg">{{ $jadwalHariIni->menu->name }}</span>
                                <span class="text-xs text-gray-500 flex items-center justify-end gap-1 mt-1 font-medium">
                                    Target: 
                                    @if($jadwalHariIni->target_type === 'sekolah' || $jadwalHariIni->target_type === 'semua')
                                        <strong class="text-blue-700 bg-blue-100 px-2 py-0.5 rounded shadow-sm">{{ number_format($totalStudents) }} Siswa</strong> 
                                    @endif
                                    
                                    @if($jadwalHariIni->target_type === 'semua') <span class="text-gray-400 font-bold">&amp;</span> @endif

                                    @if($jadwalHariIni->target_type === 'posyandu' || $jadwalHariIni->target_type === 'semua')
                                        <strong class="text-pink-700 bg-pink-100 px-2 py-0.5 rounded shadow-sm">{{ number_format($totalJiwaPosyandu) }} Posyandu</strong>
                                    @endif
                                </span>
                            </div>

                            <form action="{{ route('daily-menus.execute', $jadwalHariIni->id) }}" method="POST" onsubmit="return confirm('Konfirmasi: Selesaikan menu hari ini? Stok akan dipotong dan jadwal akan dihapus dari kalender.')">
                                @csrf
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-xl shadow-md transition-all flex items-center gap-2 active:scale-95">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Eksekusi Resep
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-gray-400 font-bold text-sm bg-gray-50 px-5 py-3 rounded-xl border border-gray-200 border-dashed">
                            💤 Tidak ada jadwal menu untuk hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($activePeriod) && count($calendarData) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl mb-6 border border-gray-200">
                <div class="p-5 bg-indigo-50/50 border-b border-indigo-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-indigo-900 flex items-center gap-2 text-lg">
                            <span class="text-xl">📅</span> Kalender Target Porsi
                        </h3>
                        <p class="text-xs text-indigo-600 mt-1 font-bold tracking-wider uppercase">Periode Aktif: {{ $activePeriod->name }}</p>
                    </div>
                    <a href="{{ route('daily-targets.index') }}" class="text-xs font-bold bg-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-sm hover:bg-indigo-700 transition-colors flex items-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Kalender Libur
                    </a>
                </div>
                
                <div class="p-5 bg-white">
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                        @foreach($calendarData as $day)
                            <div class="border rounded-xl flex flex-col overflow-hidden bg-white shadow-sm transition-all hover:shadow-md {{ $day['is_today'] ? 'border-indigo-400 ring-2 ring-indigo-200 scale-[1.02]' : 'border-gray-200' }}">
                                
                                <div class="text-center py-2 {{ $day['is_sunday'] ? 'bg-red-500 text-white' : ($day['is_today'] ? 'bg-indigo-600 text-white' : 'bg-gray-50 text-gray-700 border-b border-gray-200') }}">
                                    <div class="text-[10px] font-bold uppercase tracking-widest opacity-90">{{ $day['day_name'] }}</div>
                                    <div class="text-2xl font-black leading-none my-1">{{ $day['day_num'] }}</div>
                                    <div class="text-[10px] font-bold opacity-90">{{ $day['month'] }}</div>
                                </div>
                                
                                <div class="p-2.5 flex-1 flex flex-col justify-center">
                                    @if($day['is_sunday'])
                                        <div class="text-center">
                                            <span class="inline-block bg-red-100 text-red-600 text-[10px] font-black tracking-widest px-2 py-1 rounded-md border border-red-200">LIBUR</span>
                                        </div>
                                    @else
                                        <div class="flex justify-between items-center border-b border-gray-100 pb-1.5 mb-1.5">
                                            <span class="text-[11px] text-gray-500 font-bold">Siswa</span>
                                            <span class="text-sm font-black text-blue-600">{{ number_format($day['siswa']) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-[11px] text-gray-500 font-bold">Posyandu</span>
                                            <span class="text-sm font-black text-pink-600 {{ $day['posyandu'] > 0 ? 'bg-pink-100 px-1.5 py-0.5 rounded border border-pink-200' : '' }}">{{ number_format($day['posyandu']) }}</span>
                                        </div>
                                        
                                        @if($day['libur'] > 0)
                                            <div class="mt-2 bg-red-50 border border-red-100 text-red-600 text-[10px] font-bold text-center py-1 rounded">
                                                ⚠️ {{ $day['libur'] }} Lokasi Libur
                                            </div>
                                        @endif
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded-2xl shadow-sm mt-8 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-black text-indigo-900 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        Tutup Periode Saat Ini (Arsip)
                    </h3>
                    <p class="text-sm text-indigo-700 mt-1 font-medium">
                        Tindakan ini akan <strong>mengakhiri periode aktif</strong>. Seluruh data rekap penggunaan bahan dan target harian akan <strong>disimpan secara aman sebagai arsip</strong> untuk laporan.
                    </p>
                </div>
                
                <form action="{{ route('periods.close') }}" method="POST" onsubmit="return confirm('Tutup Buku: Apakah Anda yakin ingin menutup periode ini? Pastikan semua transaksi hari ini sudah diselesaikan.');">
                    @csrf
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white font-bold py-3 px-6 rounded-xl shadow border border-indigo-900 transition-all flex items-center gap-2 active:scale-95 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tutup Buku Periode
                    </button>
                </form>
            </div>
        </div>

        @if(isset($peringatanAlergen) && count($peringatanAlergen) > 0)
            <div class="bg-red-50 border border-red-200 p-6 rounded-2xl shadow-sm mb-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <span class="text-9xl">🚨</span>
                </div>
                <div class="relative z-10">
                    <h3 class="font-black text-red-800 flex items-center gap-2 text-xl uppercase tracking-wider mb-2">
                        <span class="text-2xl animate-pulse">🚨</span> Bahaya Alergen Menu Besok!
                    </h3>
                    <p class="text-sm text-red-700 font-bold mb-3">Sistem mendeteksi ada bahan berbahaya pada resep besok. Harap pisahkan porsi untuk penerima berikut:</p>
                    <ul class="text-sm text-red-900 font-bold space-y-2">
                        @foreach($peringatanAlergen as $peringatan)
                            <li class="bg-white/60 px-4 py-2 rounded-lg border border-red-100 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> {{ $peringatan }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-orange-100">
            <div class="p-5 bg-gradient-to-r from-orange-50 to-orange-100/50 border-b border-orange-100 flex items-center gap-3">
                <span class="text-2xl bg-white p-2 rounded-xl shadow-sm">🛒</span>
                <h3 class="font-black text-orange-900 text-lg uppercase tracking-wider">
                    Daftar Kebutuhan Belanja Besok <span class="text-orange-600">({{ date('d M Y', strtotime($besok)) }})</span>
                </h3>
            </div>
            <div class="p-6">
            @if(isset($jadwalBesokList) && $jadwalBesokList->count() > 0)
                <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($jadwalBesokList as $jadwal)
                        <div class="flex flex-col bg-white p-3 rounded-xl border border-blue-100 shadow-sm">
                            <div class="font-black text-blue-800 uppercase text-sm mb-2">🍲 {{ $jadwal->menu->name }}</div>
                            <div class="text-[11px] text-gray-500 bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-200 font-medium">
                                Target: 
                                @if($jadwal->target_type === 'sekolah' || $jadwal->target_type === 'semua')
                                    <strong class="text-blue-600">{{ number_format($totalStudents) }} Siswa</strong> 
                                @endif
                                @if($jadwal->target_type === 'semua') <span class="mx-1">&amp;</span> @endif
                                @if($jadwal->target_type === 'posyandu' || $jadwal->target_type === 'semua')
                                    <strong class="text-pink-600">{{ number_format($totalJiwaPosyandu) }} Posyandu</strong>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if(count($kebutuhanBesok) > 0)
                    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 font-bold">Bahan Harus Dibeli</th>
                                    <th class="py-3 px-4 font-bold text-center">Permintaan Dapur</th>
                                    <th class="py-3 px-4 font-bold text-center">Sisa Stok Gudang</th>
                                    <th class="py-3 px-4 font-bold text-center">Kekurangan (Beli)</th>
                                    <th class="py-3 px-4 font-bold text-right">Estimasi Biaya</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($kebutuhanBesok as $kebutuhan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 font-bold text-gray-900">
                                        {{ $kebutuhan['name'] }}
                                        @if(isset($kebutuhan['status']) && $kebutuhan['status'] == 'completed')
                                            <span class="ml-2 text-[9px] bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded-md uppercase font-black tracking-widest border border-emerald-200 shadow-sm">✔ Tiba di Gudang</span>
                                        @endif
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center text-gray-600 font-bold bg-gray-50/50 border-x border-gray-100">
                                        {{ floatval($kebutuhan['permintaan']) }} <span class="text-[10px] uppercase">{{ $kebutuhan['unit'] }}</span>
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center text-emerald-600 font-bold bg-emerald-50/30 border-r border-gray-100">
                                        {{ floatval($kebutuhan['stok']) }} <span class="text-[10px] uppercase">{{ $kebutuhan['unit'] }}</span>
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center text-red-600 font-black bg-red-50/50 border-r border-red-100">
                                        + {{ floatval($kebutuhan['defisit']) }} <span class="text-[10px] uppercase">{{ $kebutuhan['unit'] }}</span>
                                    </td>
                                    
                                    <td class="py-3 px-4 text-right text-gray-700 font-bold">
                                        Rp {{ number_format($kebutuhan['biaya'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-orange-50 border-t border-orange-200">
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-right font-black text-orange-900 uppercase tracking-widest text-xs">Total Perkiraan Biaya Pasar:</td>
                                    <td class="py-4 px-4 text-right font-black text-orange-700 text-lg">Rp {{ number_format($totalBiayaBesok, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center p-6 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 font-bold shadow-sm">
                        <span class="text-3xl block mb-2">🎉</span>
                        Stok gudang AMAN! Kebutuhan resep dapur sudah tercukupi seluruhnya oleh stok gudang saat ini.
                    </div>
                @endif
            @else
                <div class="text-center p-8 bg-gray-50 text-gray-500 rounded-xl border border-dashed border-gray-300 font-bold">
                    <span class="text-3xl block mb-2">💤</span>
                    Belum ada jadwal menu yang diatur untuk besok. Silakan atur di Kalender Jadwal Menu.
                </div>
            @endif
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            <div class="p-5 bg-slate-800 flex items-center gap-3">
                <span class="text-xl bg-slate-700 p-1.5 rounded-lg">📦</span>
                <h3 class="font-black text-white text-lg tracking-widest uppercase">Stok Logistik Gudang Saat Ini</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white text-sm">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-5 text-left font-bold">Data Barang</th>
                            <th class="py-3 px-5 text-center font-bold">Stok Sistem</th>
                            <th class="py-3 px-5 text-right font-bold">Valuasi (HPP)</th>
                            <th class="py-3 px-5 text-center font-bold">Status Indikator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-3 px-5">
                                <div class="font-bold text-gray-900 text-base">{{ $item->name }}</div>
                                <div class="text-[11px] font-bold text-gray-400 mt-0.5 bg-gray-100 w-fit px-2 py-0.5 rounded">@ Rp {{ number_format($item->hpp, 0, ',', '.') }} / {{ $item->unit }}</div>
                            </td>
                            <td class="py-3 px-5 text-center font-black text-gray-800 text-lg">{{ floatval($item->stock_system) }} <span class="text-xs text-gray-500 font-bold">{{ $item->unit }}</span></td>
                            
                            <td class="py-3 px-5 text-right font-black text-emerald-600">
                                Rp {{ number_format($item->stock_system * $item->hpp, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-5 text-center">
                                <span class="py-1 px-3 rounded-md text-[10px] uppercase tracking-wider font-black shadow-sm {{ $item->status_color }}">{{ $item->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 flex flex-col h-full">
                <div class="p-5 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-black text-white flex items-center gap-2 tracking-wider">
                        <span class="bg-white/20 p-1.5 rounded-lg">🏫</span>
                        Penerima Manfaat
                    </h3>
                    <span class="bg-white text-blue-700 text-[10px] font-black px-3 py-1 rounded-full shadow-sm uppercase tracking-widest">
                        {{ $beneficiaries->count() }} Titik
                    </span>
                </div>
                
                <div class="flex-1 max-h-[400px] overflow-y-auto p-3 space-y-2 bg-gray-50/30 custom-scrollbar">
                    @forelse($beneficiaries as $school)
                        <div x-data="{ open: false }" class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden hover:border-blue-200 transition-all">
                            <button @click="open = !open" class="w-full p-3 flex items-center justify-between focus:outline-none">
                                <div class="text-left flex-1">
                                    <div class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                        {{ $school->school_name }}
                                        @if($school->type === 'posyandu')
                                            <span class="text-[9px] bg-pink-100 text-pink-600 px-1.5 py-0.5 rounded-md uppercase tracking-wider">Posyandu</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1 flex items-center gap-2">
                                        @if($school->type === 'posyandu')
                                            <span class="font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded">{{ $school->total_balita }} Balita</span>
                                            <span class="font-bold text-pink-600 bg-pink-50 px-1.5 py-0.5 rounded">{{ $school->total_bumil_busui }} Bumil</span>
                                        @else
                                            <span class="font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $school->porsi_besar }} Besar</span>
                                            <span class="font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">{{ $school->porsi_kecil }} Kecil</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($school->allergen_count > 0)
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs font-bold border border-red-200 shadow-sm" title="Alergen">
                                            {{ $school->allergen_count }}
                                        </span>
                                    @endif
                                    <svg :class="{'rotate-180 text-blue-500': open, 'text-gray-300': !open}" class="w-4 h-4 transform transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            
                            <div x-show="open" x-collapse x-cloak>
                                <div class="px-3 pb-3 pt-1 border-t border-gray-50">
                                    <div class="bg-red-50/80 p-3 rounded-lg border border-red-100">
                                        <div class="text-xs font-bold text-red-800 mb-1 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            Detail Alergen ({{ $school->allergen_count }} Orang)
                                        </div>
                                        <div class="text-xs text-red-700/80 font-medium">
                                            {{ $school->allergen_details ?: 'Tidak ada catatan alergen khusus.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400 font-bold text-sm">Belum ada data penerima manfaat.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 flex flex-col h-full">
                <div class="p-5 bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-between rounded-t-2xl">
                    <h3 class="font-black text-white flex items-center gap-2 tracking-wider">
                        <span class="bg-white/20 p-1.5 rounded-lg">🍲</span>
                        Resep Menu Terbaru
                    </h3>
                    <a href="{{ route('menus.index') }}" class="bg-white/20 hover:bg-white/30 text-white text-[10px] font-black px-3 py-1.5 rounded-full transition-all border border-white/30 flex items-center gap-1 uppercase tracking-widest shadow-sm">
                        Lihat Semua
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                
                <div class="flex-1 max-h-[400px] overflow-y-auto p-3 space-y-2 bg-gray-50/30 custom-scrollbar">
                    @forelse($menusWithItems as $menu)
                        <div x-data="{ open: false }" class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden hover:border-emerald-200 transition-all">
                            <button @click="open = !open" class="w-full p-3 flex items-center justify-between focus:outline-none">
                                <div class="text-left flex-1">
                                    <div class="font-bold text-gray-800 text-sm">{{ $menu->name }}</div>
                                    <div class="text-[11px] text-gray-400 mt-1 font-medium italic line-clamp-1">{{ $menu->description ?: 'Tanpa keterangan' }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded border border-emerald-100 shadow-sm">{{ $menu->items->count() }} Bahan</span>
                                    <svg :class="{'rotate-180 text-emerald-500': open, 'text-gray-300': !open}" class="w-4 h-4 transform transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </button>
                            
                            <div x-show="open" x-collapse x-cloak>
                                <div class="px-3 pb-3 pt-1 border-t border-gray-50">
                                    <div class="bg-slate-50 rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="bg-slate-100 px-3 py-1.5 border-b border-gray-200 text-[10px] font-black uppercase tracking-widest text-slate-600">Total Kebutuhan (Termasuk Posyandu)</div>
                                        <div class="p-2 space-y-1.5 max-h-40 overflow-y-auto custom-scrollbar">
                                            @forelse($menu->items as $item)
                                                @php
                                                    $gBesar  = (float) ($item->pivot->gramasi_besar ?? 0);
                                                    $gKecil  = (float) ($item->pivot->gramasi_kecil ?? 0);

                                                    $totalKebutuhanGram = ($gBesar * $masterPorsiBesar) + ($gKecil * $masterPorsiKecil);
                                                    
                                                    $jmlKonversi = $totalKebutuhanGram;
                                                    $satuan = strtolower($item->unit);
                                                    if($satuan === 'kg' || $satuan === 'liter'){
                                                        $jmlKonversi = $totalKebutuhanGram / 1000;
                                                    }
                                                @endphp
                                                <div class="flex justify-between items-center bg-white border border-gray-100 px-2 py-1.5 rounded shadow-sm">
                                                    <span class="font-bold text-xs text-gray-700">{{ $item->name }}</span>
                                                    <div class="text-right">
                                                        <span class="font-black text-emerald-600 text-xs">{{ floatval($jmlKonversi) }} {{ $item->unit }}</span>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-xs text-gray-400 italic text-center py-2">Belum ada racikan bahan resep.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-400 font-bold text-sm">Belum ada menu yang dibuat.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-app-layout>