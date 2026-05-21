<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Master Bahan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex justify-end">
                <a href="{{ route('items.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition">
                    <span>+</span> Tambah Bahan Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-semibold border-b">
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
                                <tr class="hover:bg-gray-50 transition-colors">
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
                                            <a href="{{ route('items.edit', $item->id) }}" class="text-indigo-600 bg-indigo-50 px-2 py-1 rounded text-xs hover:bg-indigo-100 transition">Edit</a>
                                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs hover:bg-red-100 transition">Hapus</button>
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