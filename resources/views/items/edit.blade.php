<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Bahan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('items.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Nama Bahan</label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Satuan (Unit)</label>
                            <select name="unit" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="kg" {{ $item->unit == 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>
                                <option value="liter" {{ $item->unit == 'liter' ? 'selected' : '' }}>liter</option>
                                <option value="pcs" {{ $item->unit == 'pcs' ? 'selected' : '' }}>pcs (Biji)</option>
                                <option value="pack" {{ $item->unit == 'pack' ? 'selected' : '' }}>pack</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2">HPP (Harga Satuan)</label>
                            <input type="number" name="hpp" value="{{ old('hpp', $item->hpp) }}" class="w-full border-gray-300 rounded-md shadow-sm" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Batas Minimum</label>
                            <input type="number" name="min_stock_warning" value="{{ old('min_stock_warning', $item->min_stock_warning) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('items.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
                        <x-primary-button>Update Bahan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>