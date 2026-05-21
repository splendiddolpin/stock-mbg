<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Sekolah Penerima') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('beneficiaries.store') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                            <input type="text" name="school_name" value="{{ old('school_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah Porsi Besar</label>
                                <input type="number" name="porsi_besar" value="{{ old('porsi_besar', 0) }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah Porsi Kecil</label>
                                <input type="number" name="porsi_kecil" value="{{ old('porsi_kecil', 0) }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Anak dengan Alergen</label>
                            <input type="number" name="allergen_count" value="{{ old('allergen_count', 0) }}" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Detail Alergen</label>
                            <textarea name="allergen_details" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Contoh: 2 anak alergi kacang, 1 anak alergi susu sapi">{{ old('allergen_details') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada catatan alergen.</p>
                        </div>

                        <div class="mb-6 pb-6 border-b border-gray-200 mt-6">
                            <h3 class="block text-sm font-bold text-red-600 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Peringatan Alergi (Centang bahan yang TIDAK BOLEH diberikan)
                            </h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-red-50/30 p-4 rounded-lg border border-red-200 max-h-64 overflow-y-auto shadow-inner">
                                @foreach($items as $item)
                                    <label class="flex items-center space-x-3 bg-white p-2.5 rounded-md border border-gray-200 cursor-pointer hover:bg-red-50 hover:border-red-300 transition-colors shadow-sm">
                                        <input type="checkbox" name="allergen_items[]" value="{{ $item->id }}" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                                        <span class="text-sm font-medium text-gray-700">{{ $item->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2 italic font-medium">* Kosongkan jika seluruh penerima di lokasi ini tidak memiliki pantangan / alergi makanan.</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan Data</button>
                            <a href="{{ route('beneficiaries.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>