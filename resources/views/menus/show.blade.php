<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Resep Menu: ') }} <span class="text-green-600">{{ $menu->name }}</span>
            </h2>
            <a href="{{ route('menus.index') }}" class="text-gray-500 hover:text-gray-800 bg-gray-200 px-4 py-2 rounded-lg text-sm font-bold">Kembali</a>
            <a href="{{ route('menus.plan', $menu->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold ml-2 shadow transition">📊 Hitung Rencana Belanja</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-green-500">
                <h3 class="font-bold text-lg text-gray-800 mb-4">Tambahkan Bahan ke Resep</h3>
                
                <form action="{{ route('menus.ingredients.add', $menu->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                    @csrf
                    
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Pilih Bahan</label>
                        <select name="item_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Bahan Baku --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} (Satuan: {{ $item->unit }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full md:w-48">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Gramasi Besar (gram)</label>
                        <input type="number" step="0.0001" name="gramasi_besar" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 100" required>
                    </div>

                    <div class="w-full md:w-48">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Gramasi Kecil (gram)</label>
                        <input type="number" step="0.0001" name="gramasi_kecil" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 70" required>
                    </div>

                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition">
                            + Tambah
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Daftar Bahan untuk {{ $menu->name }}</h3>
                </div>
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 border-b">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Nama Bahan</th>
                                <th class="py-3 px-4 text-center">Gramasi Porsi Besar</th>
                                <th class="py-3 px-4 text-center">Gramasi Porsi Kecil</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($menu->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-center font-bold">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-800">{{ $item->name }}</td>
                                    
                                    <td class="py-3 px-4 text-center text-blue-600 font-bold">
                                        {{ floatval($item->pivot->gramasi_besar) }} <span class="text-xs text-gray-500">gram/{{ $item->unit }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center text-emerald-600 font-bold">
                                        {{ floatval($item->pivot->gramasi_kecil) }} <span class="text-xs text-gray-500">gram/{{ $item->unit }}</span>
                                    </td>
                                    
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('menus.ingredients.remove', [$menu->id, $item->id]) }}" method="POST" onsubmit="return confirm('Hapus bahan ini dari resep?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 bg-red-50 px-2 py-1 rounded text-xs hover:bg-red-100 font-bold transition">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 bg-gray-50/50">
                                        Belum ada bahan yang ditambahkan ke resep menu ini.
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