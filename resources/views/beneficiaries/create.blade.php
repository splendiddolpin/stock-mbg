<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight">
            🏫 {{ __('Tambah Sekolah Penerima') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('beneficiaries.store') }}" class="space-y-6">
                        @csrf
                        
                        <input type="hidden" name="type" value="sekolah">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Sekolah</label>
                            <input type="text" name="school_name" value="{{ old('school_name') }}" required placeholder="Misal: SD Negeri 1 Nusantara" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                            <div>
                                <label class="block text-sm font-bold text-blue-800 mb-1">Jumlah Porsi Besar</label>
                                <input type="number" name="porsi_besar" value="{{ old('porsi_besar', 0) }}" min="0" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-bold text-blue-700">
                                <p class="text-xs text-blue-600 mt-1">Siswa kelas 4-6 SD / SMP / SMA</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-blue-800 mb-1">Jumlah Porsi Kecil</label>
                                <input type="number" name="porsi_kecil" value="{{ old('porsi_kecil', 0) }}" min="0" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 font-bold text-blue-700">
                                <p class="text-xs text-blue-600 mt-1">Siswa PAUD / TK / kelas 1-3 SD</p>
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
                        class="mb-6 pb-6 border-b border-gray-200 mt-6 bg-red-50/20 p-5 rounded-xl border border-red-100 shadow-sm">
                        
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                                <div>
                                    <h3 class="text-base font-bold text-red-700 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Daftar Bahan Alergen Siswa
                                    </h3>
                                    <p class="text-xs text-red-500 mt-1">Cari dan pilih bahan yang <strong class="font-bold">TIDAK BOLEH</strong> dikonsumsi siswa.</p>
                                </div>
                                
                                <div class="bg-red-100 text-red-700 px-4 py-1.5 rounded-lg border border-red-200 font-bold text-sm shadow-sm flex items-center gap-2">
                                    <span class="bg-white text-red-600 px-2 py-0.5 rounded-md" x-text="selected.length"></span> 
                                    Alergen Terpilih
                                </div>
                            </div>

                            <div class="relative mb-4">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" x-model="search" placeholder="Ketik nama bahan untuk mencari..." class="w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition-all text-sm py-2.5">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
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
                                                       class="w-12 h-6 text-xs text-center border-none focus:ring-0 p-0 text-red-700 font-bold" title="Jumlah siswa">
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

                        <div class="flex items-center gap-4 mt-8 border-t pt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition-colors">Simpan Data Sekolah</button>
                            <a href="{{ route('beneficiaries.index') }}" class="text-gray-500 hover:text-gray-800 font-bold text-sm">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>