<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="bg-emerald-100 text-emerald-600 p-2 rounded-lg">♻️</span>
            {{ __('Pengembalian Sisa Bahan (Retur)') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-medium flex items-center gap-2">
                    ✅ <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl p-6">
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg">
                    <h3 class="font-bold text-emerald-800">Kapan formulir ini digunakan?</h3>
                    <p class="text-sm text-emerald-700 mt-1">Gunakan formulir ini jika bahan baku yang dimasak oleh koki <strong>lebih sedikit</strong> dari perhitungan resep sistem, sehingga ada sisa bahan di dapur yang harus dikembalikan (ditambahkan) ke dalam stok Gudang.</p>
                </div>

                <form action="{{ route('transactions.store-return') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Periode Aktif</label>
                            <select name="period_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                                @foreach($periods as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Pengembalian Sisa</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Bahan Baku yang Sisa</label>
                            <select name="item_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                                <option value="">-- Pilih Bahan --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} (Stok Saat Ini: {{ $item->stock_system }} {{ $item->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Bahan Sisa / Kembali</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 font-bold text-emerald-600" placeholder="Contoh: 5" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alasan / Keterangan (Wajib)</label>
                        <textarea name="description" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: Koki masak lebih hemat, sisa beras 5 Kg dikembalikan ke rak gudang..." required></textarea>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                            ♻️ Tambahkan Kembali ke Gudang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>