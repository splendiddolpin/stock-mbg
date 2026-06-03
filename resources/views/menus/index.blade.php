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

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-bold border-b border-gray-200">
                            <tr>
                                <th class="py-4 px-6 w-1/4">Nama Menu</th>
                                <th class="py-4 px-6 w-2/4">Keterangan</th>
                                <th class="py-4 px-6 w-1/4 text-center">Aksi (Kelola)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-green-50/30 transition-colors duration-200 group"
                                    x-show="search === '' || '{{ strtolower(addslashes($menu->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($menu->description)) }}'.includes(search.toLowerCase())">
                                    
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-800 text-base group-hover:text-green-700 transition-colors">{{ $menu->name }}</div>
                                    </td>
                                    <td class="py-4 px-6 leading-relaxed">
                                        {{ $menu->description ?: 'Tidak ada keterangan spesifik.' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('menus.show', $menu->id) }}" class="flex items-center gap-1.5 text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                Resep
                                            </a>
                                            
                                            <a href="{{ route('menus.edit', $menu->id) }}" class="text-indigo-600 bg-indigo-50 border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                                Edit
                                            </a>
                                            
                                            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini beserta semua resep di dalamnya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white transition-all duration-200 font-semibold shadow-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <p class="text-lg font-medium text-gray-500 mb-1">Belum Ada Menu Tersedia</p>
                                            <p class="text-sm">Silakan klik "Tambah Menu Baru" untuk mulai mendata sajian.</p>
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