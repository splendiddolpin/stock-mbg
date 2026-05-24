<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">🛒</span>
                {{ __('Rencana Belanja Harian Akumulatif') }}
            </h2>
            <div class="flex items-center gap-3">
                <div class="bg-white px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 shadow-sm">
                    Periode: <span class="text-blue-600">{{ $activePeriod ? $activePeriod->name : 'Tidak Ada' }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-10 print:py-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-medium flex items-center gap-2">
                    ✅ <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(!$activePeriod)
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm font-medium">
                    ⚠️ Silakan buka Periode baru terlebih dahulu.
                </div>
            @else
                
                <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl p-4 print:hidden">
                    <h3 class="font-bold text-gray-700 mb-3 px-1">Pilih Tanggal Pengadaan Belanja:</h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                        @foreach($calendarData as $day)
                            <a href="{{ route('purchase-plan.index', ['date' => $day['date']]) }}" 
                               class="block border rounded-xl flex flex-col overflow-hidden bg-white transition-all transform hover:-translate-y-1 hover:shadow-md 
                               {{ $day['is_selected'] ? 'border-blue-500 ring-2 ring-blue-200 shadow-md scale-105' : 'border-gray-200' }}">
                                
                                <div class="text-center py-2 {{ $day['is_sunday'] ? 'bg-red-500 text-white' : ($day['is_selected'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 border-b border-gray-200') }}">
                                    <div class="text-[10px] font-bold uppercase tracking-wider opacity-90">{{ $day['day_name'] }}</div>
                                    <div class="text-2xl font-black leading-none my-1">{{ $day['day_num'] }}</div>
                                    <div class="text-[10px] font-semibold opacity-90">{{ $day['month'] }}</div>
                                </div>
                                
                                <div class="p-2 text-center bg-white flex-1 flex flex-col justify-center text-[11px]">
                                    @if($day['is_sunday'])
                                        <span class="text-red-500 font-bold">LIBUR</span>
                                    @elseif($day['menu_count'] > 0)
                                        <span class="bg-blue-50 border border-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded">
                                            📦 {{ $day['menu_count'] }} Menu Masak
                                        </span>
                                    @else
                                        <span class="text-gray-400 font-medium">Kosong</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($menusHariIni->isNotEmpty())
                    <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl print:shadow-none print:border-none"
                         x-data="{ 
                             bahanBaku: [
                                 @foreach($rekapBahan as $bahan)
                                 {
                                     id: {{ $bahan['id'] }},
                                     name: '{{ $bahan['name'] }}',
                                     unit: '{{ $bahan['unit'] }}',
                                     kebutuhan: {{ floatval($bahan['total_kebutuhan']) }},
                                     pesan: {{ floatval($bahan['total_kebutuhan']) }}
                                 },
                                 @endforeach
                             ],
                             semuaBahan: [
                                 @foreach($allItems as $item)
                                 { id: {{ $item->id }}, name: '{{ addslashes($item->name) }}', unit: '{{ $item->unit }}' },
                                 @endforeach
                             ],
                             bahanManualId: '',
                             
                             tambahBahanManual() {
                                 if(!this.bahanManualId) return;
                                 let exists = this.bahanBaku.find(b => b.id == this.bahanManualId);
                                 if(exists) { alert('Bahan ini sudah ada di dalam tabel!'); return; }
                                 let master = this.semuaBahan.find(b => b.id == this.bahanManualId);
                                 if(master) {
                                     this.bahanBaku.push({ id: master.id, name: master.name + ' (Manual)', unit: master.unit, kebutuhan: 0, pesan: 0 });
                                     this.bahanManualId = '';
                                 }
                             }
                         }">
                        
                        <div class="p-5 bg-slate-50 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start gap-4 print:bg-transparent">
                            <div>
                                <h3 class="font-bold text-gray-500 text-xs uppercase tracking-wider">Tanggal Rencana Belanja</h3>
                                <div class="text-2xl font-black text-gray-900 mt-0.5">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</div>
                                
                                <div class="flex flex-wrap gap-2 mt-3 items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Menu Terjadwal:</span>
                                    @foreach($menusHariIni as $j)
                                        <span class="inline-flex items-center gap-1.5 bg-white border border-gray-200 px-3 py-1 rounded-full text-xs font-bold text-gray-700 shadow-sm">
                                            🍳 {{ $j->menu->name }} 
                                            <span class="text-[10px] uppercase font-black tracking-wide px-1.5 py-0.5 rounded {{ $j->target_type==='posyandu'?'bg-pink-100 text-pink-700':($j->target_type==='sekolah'?'bg-blue-100 text-blue-700':'bg-indigo-100 text-indigo-700') }}">
                                                {{ $j->target_type }}
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2.5 px-5 rounded-xl shadow-md text-sm transition-all flex items-center gap-2 print:hidden self-end">
                                🖨️ Cetak Formulir Belanja
                            </button>
                        </div>

                        <div class="p-4 bg-blue-50/40 border-b border-gray-200 print:hidden flex flex-col md:flex-row gap-3 items-end">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-blue-700 mb-1">🛒 Tambah Bumbu Pasar Manual (Garam, Bawang, Merica, dll):</label>
                                <select x-model="bahanManualId" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm font-medium">
                                    <option value="">-- Cari & Pilih Bumbu Dapur --</option>
                                    <template x-for="item in semuaBahan" :key="item.id">
                                        <option :value="item.id" x-text="item.name + ' (' + item.unit + ')'"></option>
                                    </template>
                                </select>
                            </div>
                            <button @click="tambahBahanManual()" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm text-sm transition-all flex items-center gap-2">
                                ➕ Tambah Bumbu
                            </button>
                        </div>

                        <form action="{{ route('purchase-plan.save-order') }}" method="POST">
                            @csrf
                            <input type="hidden" name="date_of_cooking" value="{{ $selectedDate }}">

                            <div class="overflow-x-auto p-0">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider font-bold print:bg-gray-200 print:text-black">
                                        <tr>
                                            <th class="py-3 px-4 w-1/4">Nama Bahan Baku / Bumbu</th>
                                            <th class="py-3 px-4 text-center">Kebutuhan Resep (Gabungan)</th>
                                            <th class="py-3 px-4 text-center bg-blue-600/20 print:bg-transparent border-x border-blue-500/30">Jumlah Akan Dipesan (Pasar)</th>
                                            <th class="py-3 px-4 text-center">Selisih (Buffer)</th>
                                            <th class="py-3 px-4 text-center print:hidden">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-gray-600">
                                        <template x-for="(item, index) in bahanBaku" :key="item.id">
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="py-4 px-4 font-bold text-gray-900 text-base" x-text="item.name"></td>
                                                
                                                <td class="py-4 px-4 text-center">
                                                    <span class="font-bold text-gray-500" x-text="item.kebutuhan > 0 ? item.kebutuhan.toFixed(2) : '-'"></span> 
                                                    <span class="text-[10px] text-gray-400 uppercase font-bold" x-text="item.kebutuhan > 0 ? item.unit : ''"></span>
                                                </td>
                                                
                                                <td class="py-2 px-4 text-center bg-blue-50/30 border-x border-blue-100 print:bg-transparent print:border-none">
                                                    <div class="flex justify-center items-center gap-2">
                                                        <input type="number" step="0.01" min="0" x-model.number="item.pesan" :name="`orders[${index}][qty_ordered]`"
                                                               class="w-28 text-center border-blue-300 rounded-lg font-black text-blue-700 text-lg focus:ring-blue-500 shadow-sm print:border-none print:shadow-none print:p-0">
                                                        <span class="text-[10px] font-bold text-blue-500 uppercase font-black" x-text="item.unit"></span>
                                                        
                                                        <input type="hidden" :name="`orders[${index}][item_id]`" :value="item.id">
                                                    </div>
                                                </td>

                                                <td class="py-4 px-4 text-center">
                                                    <span class="px-2 py-1 rounded font-bold text-xs"
                                                          :class="{'text-emerald-600 bg-emerald-50': item.pesan >= item.kebutuhan, 'text-red-600 bg-red-50': item.pesan < item.kebutuhan}"
                                                          x-text="(item.pesan - item.kebutuhan) >= 0 ? '+' + (item.pesan - item.kebutuhan).toFixed(2) : (item.pesan - item.kebutuhan).toFixed(2)">
                                                    </span>
                                                </td>

                                                <td class="py-4 px-4 text-center print:hidden">
                                                    <button @click="bahanBaku.splice(index, 1)" type="button" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-lg transition" title="Hapus">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-4 bg-gray-50 flex justify-end border-t border-gray-200 rounded-b-2xl print:hidden">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                    🚀 Kunci & Kirim List ke Admin Gudang
                                </button>
                            </div>
                        </form>
                        </div>
                @else
                    <div class="bg-gray-50 border border-dashed border-gray-300 text-center py-16 rounded-2xl">
                        <span class="text-4xl block mb-2">💤</span>
                        <p class="font-bold text-gray-500 text-lg">Tidak Ada Jadwal Menu Masak</p>
                        <p class="text-sm text-gray-400 mt-1">Hari ini dapur libur atau belum ada menu yang dimasukkan ke kalender jadwal.</p>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout> 