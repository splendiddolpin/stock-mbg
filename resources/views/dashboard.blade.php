@php
    // --- 1. Ambil Data Dasar ---
    $totalPorsiBesar = \App\Models\Beneficiary::sum('porsi_besar') ?? 0;
    $totalPorsiKecil = \App\Models\Beneficiary::sum('porsi_kecil') ?? 0;
    $totalBalita     = \App\Models\Beneficiary::sum('total_balita') ?? 0;
    $totalBumil      = \App\Models\Beneficiary::sum('total_bumil_busui') ?? 0;
    $menusWithItems  = \App\Models\Menu::with('items')->get();

    // --- 2. Cek Jadwal Besok & Hitung Belanjaan ---
    $besok = now()->addDay()->toDateString();
    $jadwalBesok = \App\Models\DailyMenu::with('menu.items')->where('date', $besok)->first();

    $kebutuhanBesok = [];
    $totalBiayaBesok = 0;

    if ($jadwalBesok) {
        foreach ($jadwalBesok->menu->items as $item) {
            
            // PAKSA SEMUA JADI FLOAT AGAR TIDAK ERROR
            $gBesar  = (float) ($item->pivot->gramasi_besar ?? 0);
            $gKecil  = (float) ($item->pivot->gramasi_kecil ?? 0);
            $gBalita = (float) ($item->pivot->gramasi_balita ?? 0);
            $gBumil  = (float) ($item->pivot->gramasi_bumil ?? 0);

            // RUMUS BENAR (Semua porsi diajak berhitung)
            $totalKeb = ($gBesar * $totalPorsiBesar) + 
                        ($gKecil * $totalPorsiKecil) + 
                        ($gBalita * $totalBalita) + 
                        ($gBumil * $totalBumil);

            // Cek apakah defisit (kurang) dari stok gudang
            $sisaStok = $item->stock_system - $totalKeb;
            $defisit = $sisaStok < 0 ? abs($sisaStok) : 0;

            if ($defisit > 0) {
                $biaya = $defisit * $item->hpp;
                $totalBiayaBesok += $biaya;
                $kebutuhanBesok[] = [
                    'name' => $item->name,
                    'unit' => $item->unit,
                    'defisit' => $defisit,
                    'biaya' => $biaya
                ];
            }
        }
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama MBG') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 p-6 flex flex-col justify-between">
            <div class="text-sm font-medium text-gray-500 uppercase">Penerima Manfaat</div>
            <div class="mt-2">
                <div class="text-2xl font-bold text-gray-900">
                    {{ number_format($totalStudents + $totalJiwaPosyandu) }} <span class="text-sm font-normal text-gray-500">Jiwa</span>
                </div>
                <div class="text-xs text-gray-500 font-medium mt-1 flex flex-col gap-0.5">
                    <span class="text-blue-600 font-bold">• {{ number_format($totalStudents) }} Siswa <span class="font-normal text-gray-400">({{ $totalSchools }} Sekolah)</span></span>
                    <span class="text-pink-600 font-bold">• {{ number_format($totalJiwaPosyandu) }} Orang <span class="font-normal text-gray-400">({{ $totalPosyandu }} Posyandu)</span></span>
                </div>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 p-6">
            <div class="text-sm font-medium text-gray-500 uppercase">Total Jenis Barang</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $items->count() }} <span class="text-sm font-normal text-gray-500">Item</span></div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500 p-6">
            <div class="text-sm font-medium text-gray-500 uppercase">Peringatan Stok</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ $lowStockCount }} <span class="text-sm font-normal text-gray-500">Kritis</span></div>
        </div>
    </div>
    
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">🍳 Menu Utama Hari Ini</h3>
                    <p class="text-sm text-gray-500">Tanggal: {{ now()->format('d M Y') }}</p>
                </div>

                @if($jadwalHariIni)
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="block font-bold text-indigo-600 uppercase">{{ $jadwalHariIni->menu->name }}</span>
                            <span class="text-xs text-gray-500">
                                Target: <strong class="text-blue-600">{{ number_format($totalStudents) }} Siswa</strong> & <strong class="text-pink-600">{{ number_format($totalJiwaPosyandu) }} Posyandu</strong>
                            </span>
                        </div>

                        <form action="{{ route('daily-menus.execute', $jadwalHariIni->id) }}" method="POST" onsubmit="return confirm('Konfirmasi: Selesaikan menu hari ini? Stok akan dipotong dan jadwal akan dihapus dari kalender.')">
                            @csrf
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Selesaikan & Potong Stok
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-gray-400 italic text-sm bg-gray-50 px-4 py-2 rounded-lg border border-gray-100">
                        Tidak ada jadwal menu untuk hari ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg shadow-sm mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-red-800">Tutup Periode Saat Ini</h3>
                    <p class="text-sm text-red-600 mt-1">
                        Tindakan ini akan <strong>menghapus seluruh data Rekap Penggunaan dan Barang Masuk</strong> secara permanen. Lakukan ini hanya jika Anda sudah merekap/mencetak laporan dan bersiap memulai periode baru.
                    </p>
                </div>
                
                <form action="{{ route('periods.reset') }}" method="POST" onsubmit="return confirm('PERINGATAN KERAS! Apakah Anda benar-benar yakin ingin MENGHAPUS SEMUA DATA REKAP secara permanen? Data yang dihapus tidak dapat dikembalikan!');">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-xl shadow border border-red-800 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Bersihkan Data Periode
                    </button>
                </form>
            </div>
        </div>

        @if(isset($peringatanAlergen) && count($peringatanAlergen) > 0)
            <div class="bg-red-100 border-l-4 border-red-600 p-5 rounded-lg shadow-sm mb-6 animate-pulse">
                <h3 class="font-black text-red-800 flex items-center gap-2 text-lg uppercase">
                    <span class="text-2xl">🚨</span> BAHAYA ALERGEN PADA MENU BESOK!
                </h3>
                <p class="text-sm text-red-700 mt-1 mb-2 font-medium">Harap pisahkan atau ganti bahan untuk penerima berikut:</p>
                <ul class="text-sm text-red-800 list-disc list-inside font-bold bg-white/50 p-3 rounded-md">
                    @foreach($peringatanAlergen as $peringatan)
                        <li>{{ $peringatan }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-orange-500">
            <div class="p-4 bg-orange-50/50 border-b border-orange-100 flex items-center gap-2">
                <span class="text-xl">🛒</span>
                <h3 class="font-bold text-orange-800">
                    Kebutuhan Belanja Untuk Besok ({{ date('d M Y', strtotime($besok)) }})
                </h3>
            </div>
            <div class="p-4">
            @if($jadwalBesok)
                <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-2 bg-blue-50/50 p-3 rounded-lg border border-blue-100">
                    <div class="text-sm text-gray-700">
                        Persiapan menu: <span class="font-bold text-blue-600 uppercase text-base">{{ $jadwalBesok->menu->name }}</span>
                    </div>
                    <div class="text-xs text-gray-500 bg-white px-3 py-1.5 rounded-md border border-gray-200 shadow-sm">
                        Target: <strong class="text-blue-600">{{ number_format($totalStudents) }} Siswa</strong> & <strong class="text-pink-600">{{ number_format($totalJiwaPosyandu) }} Posyandu</strong>
                    </div>
                </div>
                
                @if(count($kebutuhanBesok) > 0)
                    <div class="overflow-x-auto rounded border border-gray-200 shadow-sm">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="py-2 px-3">Bahan Harus Dibeli</th>
                                    <th class="py-2 px-3 text-center">Jumlah Kekurangan</th>
                                    <th class="py-2 px-3 text-right">Estimasi Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kebutuhanBesok as $kebutuhan)
                                <tr class="border-t hover:bg-gray-50 transition-colors">
                                    <td class="py-2 px-3 font-bold text-gray-800">{{ $kebutuhan['name'] }}</td>
                                    <td class="py-2 px-3 text-center text-red-600 font-bold bg-red-50/30">+ {{ floatval($kebutuhan['defisit']) }} {{ $kebutuhan['unit'] }}</td>
                                    <td class="py-2 px-3 text-right text-gray-700 font-semibold">Rp {{ number_format($kebutuhan['biaya'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-orange-50 font-bold text-gray-800 border-t">
                                <tr>
                                    <td colspan="2" class="py-3 px-3 text-right">Total Perkiraan Biaya:</td>
                                    <td class="py-3 px-3 text-right text-orange-700 text-base">Rp {{ number_format($totalBiayaBesok, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center p-4 bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200 font-bold text-sm shadow-sm">
                        🎉 Stok gudang AMAN! Tidak ada bahan yang perlu dibeli untuk persiapan menu besok.
                    </div>
                @endif
            @else
                <div class="text-center p-4 bg-gray-50 text-gray-500 italic rounded-md text-sm border border-gray-200 shadow-sm">
                    Belum ada jadwal menu yang diatur untuk besok. Silakan atur di Kalender Jadwal Menu.
                </div>
            @endif
</div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">📦 Stok Barang Gudang Saat Ini</div>
            <div class="overflow-x-auto p-4">
                <table class="min-w-full bg-white text-sm">
                    <thead class="bg-slate-800 text-white">
                        <tr>
                            <th class="py-2 px-4 text-left">Nama Bahan</th>
                            <th class="py-2 px-4 text-center">Stok Sistem</th>
                            <th class="py-2 px-4 text-right">Total Nilai (HPP)</th>
                            <th class="py-2 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        @foreach($items as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4">
                                <div class="font-medium text-gray-900">{{ $item->name }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">@ Rp {{ number_format($item->hpp, 0, ',', '.') }} / {{ $item->unit }}</div>
                            </td>
                            <td class="py-2 px-4 text-center font-bold">{{ floatval($item->stock_system) }} {{ $item->unit }}</td>
                            
                            <td class="py-2 px-4 text-right font-bold text-emerald-600">
                                Rp {{ number_format($item->stock_system * $item->hpp, 0, ',', '.') }}
                            </td>

                            <td class="py-2 px-4 text-center">
                                <span class="border py-1 px-3 rounded-full text-xs font-semibold {{ $item->status_color }}">{{ $item->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 flex flex-col h-full">
    <div class="p-4 bg-white border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">🏫</span>
            Daftar Sekolah & Posyandu
        </h3>
        <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-100">
            {{ $beneficiaries->count() }} Penerima
        </span>
    </div>
    
    <div class="overflow-x-auto p-0 flex-1 max-h-[400px] overflow-y-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50/80 text-xs text-gray-500 uppercase tracking-wider sticky top-0 z-10">
                <tr>
                    <th class="py-3 px-4 font-semibold">Nama Penerima</th>
                    <th class="py-3 px-4 font-semibold text-center">Porsi</th>
                    <th class="py-3 px-4 font-semibold text-center">Alergen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600">
                @forelse($beneficiaries as $school)
                    <tbody x-data="{ open: false }" class="hover:bg-blue-50/40 transition-colors duration-200">
                        <tr @click="open = !open" class="cursor-pointer group">
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                                            {{ $school->school_name }}
                                            @if($school->type === 'posyandu')
                                                <span class="ml-1 text-[10px] bg-pink-100 text-pink-600 px-1.5 py-0.5 rounded-full">Posyandu</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Ketuk untuk detail alergen</div>
                                    </div>
                                    <svg :class="{'rotate-180 text-blue-500': open, 'text-gray-300': !open}" class="w-4 h-4 transform transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </td>
                            
                            <td class="py-3 px-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if($school->type === 'posyandu')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-100 w-20 justify-center">
                                            {{ $school->total_balita }} Balita
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-100 w-20 justify-center">
                                            {{ $school->total_bumil_busui }} Bumil
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 w-16 justify-center">
                                            {{ $school->porsi_besar }} B
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 w-16 justify-center">
                                            {{ $school->porsi_kecil }} K
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="py-3 px-4 text-center">
                                @if($school->allergen_count > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs font-bold border border-red-200">
                                        {{ $school->allergen_count }}
                                    </span>
                                @else
                                    <span class="text-gray-300 font-bold">-</span>
                                @endif
                            </td>
                        </tr>
                        
                        <tr x-show="open" x-collapse x-cloak>
                            <td colspan="3" class="p-0 border-t-0">
                                <div class="bg-red-50/50 px-4 py-3 text-xs text-red-800 border-l-2 border-red-400 m-2 rounded-r-md">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        <div>
                                            <span class="font-bold block mb-0.5">Detail Alergen ({{ $school->allergen_count }} Orang):</span>
                                            <span class="text-red-600/90 leading-relaxed">{{ $school->allergen_details ?: 'Belum ada catatan alergen spesifik.' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 px-4 text-center text-gray-500">
                            Belum ada data sekolah atau posyandu penerima.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">🍲 Master Menu & Estimasi Kebutuhan Resep</div>
                <div class="overflow-x-auto p-0 flex-1 max-h-[400px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-green-600 text-white sticky top-0 z-10">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Nama Menu</th>
                                <th class="py-3 px-4 font-semibold text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @foreach($menusWithItems as $menu)
                                <tbody x-data="{ open: false }" class="hover:bg-green-50/40 transition-colors duration-200">
                                    <tr @click="open = !open" class="cursor-pointer group">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-bold text-gray-800 group-hover:text-green-600 transition-colors">{{ $menu->name }}</div>
                                                    <div class="text-[10px] text-gray-400 mt-0.5">Ketuk untuk lihat total resep</div>
                                                </div>
                                                <svg :class="{'rotate-180 text-green-500': open, 'text-gray-300': !open}" class="w-4 h-4 transform transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center text-xs">{{ $menu->description ?: '-' }}</td>
                                    </tr>
                                    
                                    <tr x-show="open" x-collapse x-cloak>
                                        <td colspan="2" class="p-0 border-t-0">
                                            <div class="bg-green-50/50 px-4 py-3 text-sm text-green-900 border-l-2 border-green-400 m-2 rounded-r-md">
                                                <div class="font-bold mb-2 text-xs uppercase tracking-wider text-green-700">Total Kebutuhan (Termasuk Posyandu):</div>
                                                
                                                @if($menu->items->count() > 0)
                                                    <ul class="space-y-1">
                                                        @foreach($menu->items as $item)
                                                            @php
                                                                $gBesar  = (float) ($item->pivot->gramasi_besar ?? 0);
                                                                $gKecil  = (float) ($item->pivot->gramasi_kecil ?? 0);
                                                                $gBalita = (float) ($item->pivot->gramasi_balita ?? 0);
                                                                $gBumil  = (float) ($item->pivot->gramasi_bumil ?? 0);

                                                                // RUMUS BENAR DI TABEL BAWAH:
                                                                $totalKecil = ($gBesar * $totalPorsiBesar) + 
                                                                              ($gKecil * $totalPorsiKecil) + 
                                                                              ($gBalita * $totalBalita) + 
                                                                              ($gBumil * $totalBumil);
                                                                
                                                                $jmlKonversi = $totalKecil;
                                                                // (Hanya untuk tampilan tabel Master Menu, bukan untuk belanja atas)
                                                                $satuan = strtolower($item->unit);
                                                                if($satuan === 'kg' || $satuan === 'liter'){
                                                                    $jmlKonversi = $totalKecil / 1000;
                                                                }
                                                            @endphp
                                                            <li class="flex justify-between items-center bg-white px-2 py-1.5 rounded shadow-sm">
                                                                <span class="font-bold text-gray-700">{{ $item->name }}</span>
                                                                <div class="text-right">
                                                                    <span class="font-bold text-blue-600">{{ floatval($jmlKonversi) }} {{ $item->unit }}</span>
                                                                    <span class="text-xs text-gray-400 block mt-0.5">(Total {{ floatval($totalKecil) }} murni)</span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="text-gray-500 italic text-xs">Belum ada bahan yang ditambahkan ke resep ini.</div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>