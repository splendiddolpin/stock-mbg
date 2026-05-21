<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu & Resep') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('menus.update', $menu->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-gray-700 mb-4">1. Informasi Menu</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Nama Menu</label>
                            <input type="text" name="name" value="{{ old('name', $menu->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Keterangan / Deskripsi</label>
                            <textarea name="description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200">{{ old('description', $menu->description) }}</textarea>
                        </div>
                    </div>

                    <div x-data="{ 
                            ingredients: [
                                @foreach($menu->items as $index => $item)
                                { 
                                    id: 'old_{{ $item->id }}_{{ $index }}', 
                                    item_id: '{{ $item->id }}', 
                                    gramasi_besar: '{{ $item->pivot->gramasi_besar }}', 
                                    gramasi_kecil: '{{ $item->pivot->gramasi_kecil }}', 
                                    gramasi_balita: '{{ $item->pivot->gramasi_balita }}', 
                                    gramasi_bumil: '{{ $item->pivot->gramasi_bumil }}' 
                                },
                                @endforeach
                            ] 
                        }" class="mb-6">
                        
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg text-gray-700">2. Racik Resep (Bahan Baku)</h3>
                            <button type="button" @click="ingredients.push({ id: Date.now(), item_id: '', gramasi_besar: '', gramasi_kecil: '', gramasi_balita: '', gramasi_bumil: '' })" class="bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold py-1.5 px-3 rounded text-sm transition">
                                + Tambah Baris Bahan
                            </button>
                        </div>

                        <div class="grid grid-cols-12 gap-2 font-bold text-xs text-center mb-2 px-1">
                            <div class="col-span-3 text-left text-gray-600">Pilih Bahan</div>
                            <div class="col-span-2 text-blue-600">Gramasi Besar</div>
                            <div class="col-span-2 text-emerald-600">Gramasi Kecil</div>
                            <div class="col-span-2 text-orange-600">Gramasi Balita</div>
                            <div class="col-span-2 text-pink-600">Gramasi Bumil</div>
                            <div class="col-span-1 text-gray-600">Aksi</div>
                        </div>

                        <template x-for="(ing, index) in ingredients" :key="ing.id">
                            <div class="grid grid-cols-12 gap-2 mb-3 items-start bg-gray-50 p-2 rounded border border-gray-100 relative hover:bg-gray-100 transition-colors">
                                
                                <div class="col-span-3">
                                    <select :name="`ingredients[${index}][item_id]`" x-model="ing.item_id" class="w-full border-gray-300 rounded shadow-sm text-sm" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-span-2">
                                    <input type="number" step="0.01" :name="`ingredients[${index}][gramasi_besar]`" x-model="ing.gramasi_besar" class="w-full border-blue-200 bg-blue-50/30 rounded shadow-sm text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Gram" required>
                                </div>
                                
                                <div class="col-span-2">
                                    <input type="number" step="0.01" :name="`ingredients[${index}][gramasi_kecil]`" x-model="ing.gramasi_kecil" class="w-full border-emerald-200 bg-emerald-50/30 rounded shadow-sm text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200" placeholder="Gram" required>
                                </div>

                                <div class="col-span-2">
                                    <input type="number" step="0.01" :name="`ingredients[${index}][gramasi_balita]`" x-model="ing.gramasi_balita" class="w-full border-orange-200 bg-orange-50/30 rounded shadow-sm text-sm focus:border-orange-500 focus:ring focus:ring-orange-200" placeholder="Gram" required>
                                </div>

                                <div class="col-span-2">
                                    <input type="number" step="0.01" :name="`ingredients[${index}][gramasi_bumil]`" x-model="ing.gramasi_bumil" class="w-full border-pink-200 bg-pink-50/30 rounded shadow-sm text-sm focus:border-pink-500 focus:ring focus:ring-pink-200" placeholder="Gram" required>
                                </div>
                                
                                <div class="col-span-1 flex justify-center mt-1">
                                    <button type="button" @click="ingredients.splice(index, 1)" class="text-red-500 hover:text-red-700 p-1 bg-white rounded shadow-sm border border-red-100" title="Hapus Baris">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                
                            </div>
                        </template>

                        <div x-show="ingredients.length === 0" class="text-center py-4 text-gray-500 italic text-sm border-2 border-dashed border-gray-200 rounded">
                            Resep masih kosong. Klik tombol "+ Tambah Baris Bahan" untuk menyusun komposisi.
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('menus.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Batal</a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">Update Menu & Resep</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>