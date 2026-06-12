<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                
                <form action="{{ route('transactions.storeIn') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Periode</label>
                        <select name="period_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 font-semibold" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}">
                                    {{ $period->name }} {{ $period->is_active ? '🔥 (Aktif Hari Ini)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Barang</label>
                        <select name="item_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} (Stok saat ini: {{ $item->stock_system }} {{ $item->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Masuk</label>
                            <input type="number" step="0.01" name="quantity" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Masuk</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan / Supplier</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('transactions.in') }}" class="text-gray-600 mr-4 hover:text-gray-900 font-semibold transition-colors">Batal</a>
                        <x-primary-button class="rounded-xl px-6 py-3">
                            Simpan Barang Masuk
                        </x-primary-button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>