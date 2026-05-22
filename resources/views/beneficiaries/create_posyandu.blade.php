<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-pink-700 leading-tight">
            👶 {{ __('Tambah Data Posyandu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-pink-500">
                
                <form action="{{ route('beneficiaries.store') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="type" value="posyandu">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Posyandu / Desa</label>
                        <input type="text" name="school_name" class="shadow-sm border-gray-300 rounded-md w-full focus:border-pink-500 focus:ring-pink-500" placeholder="Misal: Posyandu Mawar 1" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6 mt-6">
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-100">
                            <label class="block text-orange-800 text-sm font-bold mb-2">Total Balita</label>
                            <input type="number" name="total_balita" class="shadow-sm border-orange-300 rounded-md w-full focus:border-orange-500 focus:ring-orange-500 font-bold text-orange-700" value="0" min="0" required>
                            <p class="text-xs text-orange-600 mt-1">Anak usia 0-5 tahun</p>
                        </div>

                        <div class="bg-pink-50 p-4 rounded-lg border border-pink-100">
                            <label class="block text-pink-800 text-sm font-bold mb-2">Total Bumil / Busui</label>
                            <input type="number" name="total_bumil_busui" class="shadow-sm border-pink-300 rounded-md w-full focus:border-pink-500 focus:ring-pink-500 font-bold text-pink-700" value="0" min="0" required>
                            <p class="text-xs text-pink-600 mt-1">Ibu Hamil & Menyusui</p>
                        </div>
                    </div>

                    <div x-data="{ 
                            search: '',
                            items: {{ $items->map(fn($item) => ['id' => $item->id, 'name' => $item->name])->toJson() }},
                            selected: [],
                            get filteredItems() {
                                return this.items.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                            }
                        }" 
                        class="mb-6 pb-6 mt-6 bg-red-50/20 p-5 rounded-xl border border-red-100 shadow-sm">
                        
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-red-700 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Peringatan Alergen Posyandu
                                </h3>
                                <p class="text-xs text-red-500 mt-1">Cari dan centang bahan, lalu masukkan <strong class="font-bold">jumlah jiwa</strong> yang alergi.</p>
                            </div>
                            
                            <div class="bg-red-100 text-red-700 px-4 py-1.5 rounded-lg border border-red-200 font-bold text-sm shadow-sm flex items-center gap-2">
                                <span class="bg-white text-red-600 px-2 py-0.5 rounded-md" x-text="selected.length"></span> 
                                Bahan Terpilih
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="search" placeholder="Ketik nama bahan alergen (misal: Telur)..." class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition-all text-sm py-2.5">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="item in filteredItems" :key="item.id">
                                <div class="flex items-center justify-between bg-white p-3 rounded-lg border transition-all shadow-sm group"
                                     :class="{'border-red-400 ring-1 ring-red-400 bg-red-50/50': selected.includes(item.id.toString()), 'border-gray-200 hover:border-red-300 hover:bg-gray-50': !selected.includes(item.id.toString())}">
                                    
                                    <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                        <input type="checkbox" name="allergen_items[]" :value="item.id" x-model="selected" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer">
                                        <span class="text-sm font-semibold text-gray-700 group-hover:text-red-700 transition-colors line-clamp-1" x-text="item.name"></span>
                                    </label>

                                    <div x-show="selected.includes(item.id.toString())" x-collapse>
                                        <div class="flex items-center gap-1.5 border border-red-200 bg-white rounded p-1 shadow-sm">
                                            <svg class="w-3.5 h-3.5 text-red-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <input type="number" :name="`allergen_counts[${item.id}]`" min="1" value="1" 
                                                   class="w-12 h-6 text-xs text-center border-none focus:ring-0 p-0 text-red-700 font-bold" title="Jumlah jiwa">
                                        </div>
                                    </div>
                                    
                                </div>
                            </template>

                            <div x-show="filteredItems.length === 0" class="col-span-full text-center py-8 bg-white border border-dashed border-gray-300 rounded-lg">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-gray-500 text-sm font-medium">Bahan <span class="text-gray-700 font-bold" x-text="`'${search}'`"></span> tidak ditemukan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-8 border-t pt-4">
                        <a href="{{ route('beneficiaries.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm">Batal</a>
                        <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-lg shadow transition-colors">
                            Simpan Data Posyandu
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>