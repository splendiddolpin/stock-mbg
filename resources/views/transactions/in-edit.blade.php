<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('transactions.updateIn', $transaction->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Periode</label>
                        <select name="period_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Periode Stock Opname --</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ $transaction->period_id == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Barang</label>
                        <select name="item_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ $transaction->item_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} (Stok saat ini: {{ $item->stock_system }} {{ $item->unit }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-orange-500 mt-1">Catatan: Mengubah barang akan otomatis menyesuaikan stok barang lama dan barang baru.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Masuk</label>
                            <input type="number" name="quantity" value="{{ old('quantity', $transaction->quantity) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Masuk</label>
                            <input type="date" name="date" value="{{ old('date', $transaction->date) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan / Supplier</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $transaction->description) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('transactions.in') }}" class="text-gray-600 mr-4 hover:underline">Batal</a>
                        <x-primary-button>
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>