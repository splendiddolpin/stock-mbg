<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftarkan Bahan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('items.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Nama Bahan</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Minyak Goreng" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Satuan (Unit)</label>
                            <select name="unit" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="kg">kg (Kilogram)</option>
                                <option value="liter">liter</option>
                                <option value="pcs">pcs (Biji)</option>
                                <option value="pack">pack</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2">HPP (Harga Satuan)</label>
                            <input type="number" name="hpp" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 15000" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Batas Minimum</label>
                            <input type="number" name="min_stock_warning" class="w-full border-gray-300 rounded-md shadow-sm" value="10" required>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button>Simpan Bahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>