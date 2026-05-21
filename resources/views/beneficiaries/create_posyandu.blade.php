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

                    <div class="col-span-2 bg-red-50 p-4 rounded-lg border border-red-100 mt-2">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-red-700">Jumlah Orang dengan Alergen</label>
                            <input type="number" name="allergen_count" value="{{ old('allergen_count', 0) }}" min="0" required class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-red-700">Detail Alergen</label>
                            <textarea name="allergen_details" rows="2" class="mt-1 block w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200" placeholder="Contoh: 2 balita alergi telur, 1 bumil alergi udang">{{ old('allergen_details') }}</textarea>
                            <p class="text-xs text-red-500 mt-1">Kosongkan jika tidak ada catatan alergen.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8 border-t pt-4">
                        <a href="{{ route('beneficiaries.index') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm">Batal</a>
                        <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-lg shadow">
                            Simpan Data Posyandu
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>