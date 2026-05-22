<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Resep Menu: ') }} <span class="text-green-600 font-black">{{ $menu->name }}</span>
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('menus.index') }}" class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 border border-gray-200 px-4 py-2 rounded-lg text-sm font-bold transition">Kembali</a>
                <a href="{{ route('menus.plan', $menu->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Hitung Rencana Belanja
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-green-500">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 p-1.5 rounded-md">➕</span>
                    Tambahkan Bahan ke Resep
                </h3>
                
                <form action="{{ route('menus.ingredients.add', $menu->id) }}" method="POST" 
                      x-data="{ 
                          selectedItem: '', 
                          satuanItems: {
                              @foreach($items as $item)
                              '{{ $item->id }}': '{{ strtolower($item->unit) }}',
                              @endforeach
                          },
                          getUnitLabel() {
                              if(!this.selectedItem) return '...';
                              let unit = this.satuanItems[this.selectedItem];
                              if(unit === 'kg') return 'gram';
                              if(unit === 'liter') return 'ml';
                              return unit;
                          }
                      }" 
                      class="flex flex-col md:flex-row gap-4 items-end bg-gray-50 p-4 rounded-xl border border-gray-100">
                    @csrf
                    
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold mb-2 text-gray-600 uppercase tracking-wide">Pilih Bahan Baku</label>
                        <select name="item_id" x-model="selectedItem" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" required>
                            <option value="">-- Cari & Pilih Bahan --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (Satuan Gudang: {{ $item->unit }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full md:w-56 relative">
                        <label class="block text-xs font-bold mb-2 text-blue-600 uppercase tracking-wide">Porsi Besar <span class="text-gray-400 normal-case">(Siswa 4-6 / Bumil)</span></label>
                        <input type="number" step="0.0001" min="0" name="gramasi_besar" class="w-full border-blue-300 bg-blue-50 focus:bg-white rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 font-bold text-blue-700 pr-12" placeholder="0" required>
                        <div class="absolute inset-y-0 bottom-0 right-0 pr-3 flex items-center pointer-events-none mb-[2px]">
                            <span class="text-blue-400 text-xs font-bold uppercase" x-text="getUnitLabel()"></span>
                        </div>
                    </div>

                    <div class="w-full md:w-56 relative">
                        <label class="block text-xs font-bold mb-2 text-emerald-600 uppercase tracking-wide">Porsi Kecil <span class="text-gray-400 normal-case">(Siswa 1-3 / Balita)</span></label>
                        <input type="number" step="0.0001" min="0" name="gramasi_kecil" class="w-full border-emerald-300 bg-emerald-50 focus:bg-white rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 font-bold text-emerald-700 pr-12" placeholder="0" required>
                        <div class="absolute inset-y-0 bottom-0 right-0 pr-3 flex items-center pointer-events-none mb-[2px]">
                            <span class="text-emerald-400 text-xs font-bold uppercase" x-text="getUnitLabel()"></span>
                        </div>
                    </div>

                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition transform active:scale-95">
                            Tambah Bahan
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 flex items-center gap-2">
                        <span>📜</span> Daftar Bahan untuk {{ $menu->name }}
                    </h3>
                    <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">{{ $menu->items->count() }} Jenis Bahan</span>
                </div>
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-800 text-white">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Nama Bahan</th>
                                <th class="py-3 px-4 text-center">Porsi Besar (Siswa 4-6 / Bumil)</th>
                                <th class="py-3 px-4 text-center">Porsi Kecil (Siswa 1-3 / Balita)</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($menu->items as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-4 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                                    <td class="py-4 px-4 font-bold text-gray-800 text-base">{{ $item->name }}</td>
                                    
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg font-bold border border-blue-200">
                                            {{ floatval($item->pivot->gramasi_besar) }} 
                                            <span class="text-[10px] font-bold opacity-70 uppercase tracking-wider">
                                                {{ (strtolower($item->unit) === 'kg') ? 'GRAM' : ((strtolower($item->unit) === 'liter') ? 'ML' : $item->unit) }}
                                            </span>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg font-bold border border-emerald-200">
                                            {{ floatval($item->pivot->gramasi_kecil) }} 
                                            <span class="text-[10px] font-bold opacity-70 uppercase tracking-wider">
                                                {{ (strtolower($item->unit) === 'kg') ? 'GRAM' : ((strtolower($item->unit) === 'liter') ? 'ML' : $item->unit) }}
                                            </span>
                                        </span>
                                    </td>
                                    
                                    <td class="py-4 px-4 text-center">
                                        <form action="{{ route('menus.ingredients.remove', [$menu->id, $item->id]) }}" method="POST" onsubmit="return confirm('Hapus bahan ini dari resep?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white font-bold transition shadow-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400 gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <p class="font-medium text-gray-500">Belum ada bahan yang ditambahkan ke resep menu ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>