<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Master Bahan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input x-model="search" type="text" placeholder="Cari nama bahan baku..." class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all outline-none">
                    
                    <button x-show="search !== ''" @click="search = ''" style="display: none;" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <a href="{{ route('items.create') }}" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-lg flex items-center justify-center gap-2 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Bahan Baru</span>
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                            <tr>
                                <th class="py-3 px-4">Nama Bahan</th>
                                <th class="py-3 px-4 text-center">Stok & Satuan</th>
                                <th class="py-3 px-4 text-center">Batas Kritis</th>
                                <th class="py-3 px-4 text-right">HPP</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50 transition-colors"
                                    x-show="search === '' || '{{ strtolower(addslashes($item->name)) }}'.includes(search.toLowerCase())">
                                    
                                    <td class="py-3 px-4 font-bold text-gray-800">{{ $item->name }}</td>
                                    <td class="py-3 px-4 text-center font-bold text-blue-600">
                                        {{ $item->stock_system }} {{ $item->unit }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-red-500">
                                        {{ $item->min_stock_warning }} {{ $item->unit }}
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        Rp {{ number_format($item->hpp, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('items.edit', $item->id) }}" class="text-indigo-600 bg-indigo-50 px-2 py-1 rounded text-xs hover:bg-indigo-100 transition font-bold">Edit</a>
                                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs hover:bg-red-100 transition font-bold">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">Belum ada data bahan yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>