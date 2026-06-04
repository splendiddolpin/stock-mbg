<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-green-100 text-green-600 p-2 rounded-lg">🍲</span>
                {{ __('Kelola Master Menu') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ search: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm relative animate-fade-in-down">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-orange-100 border-l-4 border-orange-500 p-4 text-orange-700 font-medium rounded shadow-sm mb-4">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input x-model="search" type="text" placeholder="Cari nama menu atau keterangan..." class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm shadow-sm transition-all outline-none">
                    
                    <button x-show="search !== ''" @click="search = ''" style="display: none;" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <a href="{{ route('menus.create') }}" class="w-full sm:w-auto group bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-bold py-2.5 px-5 rounded-xl shadow-md hover:shadow-lg transform transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Menu Baru</span>
                </a>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="overflow-x-auto p-0 rounded-2xl">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-bold border-b border-gray-200">
                            <tr>
                                <th class="py-4 px-6 w-1/4">Nama Menu</th>
                                <th class="py-4 px-6 w-2/4">Keterangan</th>
                                <th class="py-4 px-6 w-1/4 text-center">Aksi (Kelola)</th>
                            </tr>
                        </thead>
                        
                        @forelse($menus as $menu)
                            <tbody x-data="{ expanded: false }" 
                                   x-show="search === '' || '{{ strtolower(addslashes($menu->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($menu->description)) }}'.includes(search.toLowerCase())"
                                   class="border-b border-gray-100 last:border-b-0">
                                
                                <tr @click="expanded = !expanded" class="hover:bg-green-50/40 transition-colors duration-200 group cursor-pointer" :class="{'bg-green-50/20': expanded}">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <svg :class="{'rotate-90 text-green-600': expanded, 'text-gray-400 group-hover:text-green-500': !expanded}" class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                            
                                            <div class="font-black text-gray-800 text-base group-hover:text-green-700 transition-colors">{{ $menu->name }}</div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 leading-relaxed text-gray-600">
                                        {{ $menu->description ?: 'Tidak ada keterangan spesifik.' }}
                                    </td>
                                    
                                    <td class="py-4 px-6" @click.stop>
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('menus.show', $menu->id) }}" class="flex items-center gap-1 text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200 font-bold shadow-sm text-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Kelola Resep
                                            </a>
                                            
                                            <a href="{{ route('menus.edit', $menu->id) }}" class="text-indigo-600 bg-indigo-50 border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-bold shadow-sm text-xs">
                                                Edit
                                            </a>
                                            
                                            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini beserta semua resep di dalamnya?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 font-bold shadow-sm text-xs">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <tr x-show="expanded" x-cloak class="bg-slate-50/50">
                                    <td colspan="3" class="p-0">
                                        <div x-show="expanded" x-collapse>
                                            <div class="p-6 border-t border-gray-100">
                                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                                                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                                                        <h4 class="font-black text-green-800 flex items-center gap-2">
                                                            <span class="text-lg">📋</span> Komposisi Bahan Baku (Resep)
                                                        </h4>
                                                        <span class="text-xs font-bold bg-green-100 text-green-700 px-2.5 py-1 rounded-md">
                                                            {{ $menu->items ? $menu->items->count() : 0 }} Macam Bahan
                                                        </span>
                                                    </div>
                                                    
                                                    @if($menu->items && $menu->items->count() > 0)
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach($menu->items as $item)
                                                                <div class="flex justify-between items-center p-3 bg-gray-50 border border-gray-100 rounded-lg hover:border-green-200 transition-colors">
                                                                    <div class="font-bold text-gray-700">{{ $item->name }}</div>
                                                                    <div class="text-[11px] font-black flex gap-2">
                                                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded shadow-sm">Besar: {{ floatval($item->pivot->gramasi_besar ?? 0) }} {{ $item->unit }}</span>
                                                                        <span class="bg-pink-100 text-pink-700 px-2 py-1 rounded shadow-sm">Kecil: {{ floatval($item->pivot->gramasi_kecil ?? 0) }} {{ $item->unit }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="flex flex-col items-center justify-center py-6 text-gray-400 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
                                                            <span class="text-3xl mb-2">🍽️</span>
                                                            <p class="font-medium text-sm">Resep kosong.</p>
                                                            <p class="text-xs mt-1">Silakan klik tombol "Kelola Resep" untuk mulai menakar bahan.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @empty
                            <tbody>
                                <tr>
                                    <td colspan="3" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <p class="text-lg font-medium text-gray-500 mb-1">Belum Ada Menu Tersedia</p>
                                            <p class="text-sm">Silakan klik "Tambah Menu Baru" untuk mulai mendata sajian.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endforelse
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>