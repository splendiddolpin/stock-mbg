<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Menu & Resep Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> 
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('menus.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="font-bold text-lg text-gray-700 mb-4 flex items-center gap-2">
                            <span class="bg-blue-100 text-blue-700 w-6 h-6 flex items-center justify-center rounded-full text-sm">1</span> 
                            Informasi Menu
                        </h3>
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Nama Menu</label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Contoh: Nasi Goreng Spesial" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Keterangan / Deskripsi</label>
                            <textarea name="description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Contoh: Nasi goreng dengan tambahan telur dan ayam suwir"></textarea>
                        </div>
                    </div>

                    <div x-data="{ 
                            ingredients: [
                                { id: Date.now(), item_id: '', gramasi_besar: '', gramasi_kecil: '' }
                            ],
                            // Kita buat kamus satuan dari database
                            satuanItems: {
                                @foreach($items as $item)
                                '{{ $item->id }}': '{{ strtolower($item->unit) }}',
                                @endforeach
                            },
                            // Fungsi penerjemah satuan
                            getUnitLabel(itemId) {
                                if(!itemId) return '...';
                                let unit = this.satuanItems[itemId];
                                if(unit === 'kg') return 'gram';
                                if(unit === 'liter') return 'ml';
                                return unit; // Untuk pcs, butir, bungkus, dll
                            }
                        }" class="mb-6">
                        
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg text-gray-700 flex items-center gap-2">
                                <span class="bg-blue-100 text-blue-700 w-6 h-6 flex items-center justify-center rounded-full text-sm">2</span> 
                                Racik Resep (Bahan Baku)
                            </h3>
                            <button type="button" @click="ingredients.push({ id: Date.now(), item_id: '', gramasi_besar: '', gramasi_kecil: '' })" class="bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold py-1.5 px-3 rounded text-sm transition shadow-sm">
                                + Tambah Baris Bahan
                            </button>
                        </div>

                        <div class="grid grid-cols-12 gap-3 font-bold text-xs text-center mb-2 px-2">
                            <div class="col-span-5 text-left text-gray-600">Pilih Bahan dari Gudang</div>
                            <div class="col-span-3 text-blue-600">Porsi Besar (Siswa 4-6 / Bumil)</div>
                            <div class="col-span-3 text-emerald-600">Porsi Kecil (Siswa 1-3 / Balita)</div>
                            <div class="col-span-1 text-gray-600">Hapus</div>
                        </div>

                        <template x-for="(ing, index) in ingredients" :key="ing.id">
                            <div class="grid grid-cols-12 gap-3 mb-3 items-center bg-gray-50 p-2.5 rounded-lg border border-gray-200 hover:bg-white transition-colors shadow-sm">
                                
                                <div class="col-span-5">
                                    <select :name="`ingredients[${index}][item_id]`" x-model="ing.item_id" class="w-full border-gray-300 rounded shadow-sm text-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                                        <option value="">-- Cari & Pilih Bahan --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-span-3 relative">
                                    <input type="number" step="0.01" min="0" :name="`ingredients[${index}][gramasi_besar]`" x-model="ing.gramasi_besar" class="w-full border-blue-300 bg-blue-50 focus:bg-white rounded shadow-sm text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-bold text-blue-700 pr-12" placeholder="0" required>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-blue-400 text-xs font-bold uppercase" x-text="getUnitLabel(ing.item_id)"></span>
                                    </div>
                                </div>
                                
                                <div class="col-span-3 relative">
                                    <input type="number" step="0.01" min="0" :name="`ingredients[${index}][gramasi_kecil]`" x-model="ing.gramasi_kecil" class="w-full border-emerald-300 bg-emerald-50 focus:bg-white rounded shadow-sm text-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 font-bold text-emerald-700 pr-12" placeholder="0" required>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-emerald-400 text-xs font-bold uppercase" x-text="getUnitLabel(ing.item_id)"></span>
                                    </div>
                                </div>
                                
                                <div class="col-span-1 flex justify-center">
                                    <button type="button" @click="ingredients.splice(index, 1)" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded transition" title="Hapus Baris">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                
                            </div>
                        </template>

                        <div x-show="ingredients.length === 0" class="text-center py-6 text-gray-500 bg-gray-50 italic text-sm border-2 border-dashed border-gray-300 rounded-lg">
                            Belum ada bahan yang ditambahkan ke resep ini.<br>Klik tombol <strong>"+ Tambah Baris Bahan"</strong> di atas.
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4 pt-5 border-t border-gray-200">
                        <a href="{{ route('menus.index') }}" class="text-gray-500 hover:text-gray-800 font-bold text-sm transition">Batal</a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-lg shadow-md transition transform active:scale-95">Simpan Menu & Resep</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>