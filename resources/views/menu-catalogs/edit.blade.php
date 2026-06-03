<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Edit Katalog Menu</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <form action="{{ route('menu-catalogs.update', $menuCatalog->id) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Menu</label>
                        <select name="category" class="w-full border-gray-300 rounded-xl focus:ring-blue-500" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $menuCatalog->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Menu Makanan/Buah</label>
                        <input type="text" name="name" value="{{ $menuCatalog->name }}" class="w-full border-gray-300 rounded-xl focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Harga Menu (Rp)</label>
                        <input type="number" name="price" value="{{ $menuCatalog->price }}" class="w-full border-gray-300 rounded-xl focus:ring-blue-500" required>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <a href="{{ route('menu-catalogs.index') }}" class="bg-gray-100 text-gray-600 font-bold py-2 px-6 rounded-xl hover:bg-gray-200 transition-colors">Batal</a>
                        <button type="submit" class="bg-amber-500 text-white font-bold py-2 px-6 rounded-xl hover:bg-amber-600 transition-colors">Update Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>