<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex justify-end">
                <a href="{{ route('transactions.createIn') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">+ Input Barang Masuk</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Barang</th>
                            <th class="py-3 px-4">Periode</th>
                            <th class="py-3 px-4 text-center">Jumlah Masuk</th>
                            <th class="py-3 px-4 text-right">Nilai HPP</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transactions as $trx)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4">{{ date('d M Y', strtotime($trx->date)) }}</td>
                                <td class="py-3 px-4 font-bold">{{ $trx->item->name }}</td>
                                <td class="py-3 px-4">{{ $trx->period->name }}</td>
                                <td class="py-3 px-4 text-center bg-blue-50 text-blue-700 font-bold">+{{ $trx->quantity }} {{ $trx->item->unit }}</td>
                                
                                <td class="py-3 px-4 text-right">
                                    <div class="text-gray-900">@ Rp {{ number_format($trx->item->hpp, 0, ',', '.') }} <span class="text-xs text-gray-400">/{{ $trx->item->unit }}</span></div>
                                    <div class="text-xs text-emerald-600 font-bold mt-0.5">Total: Rp {{ number_format($trx->quantity * $trx->item->hpp, 0, ',', '.') }}</div>
                                </td>
                                
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('transactions.editIn', $trx->id) }}" class="text-indigo-600 hover:bg-indigo-100 bg-indigo-50 px-2 py-1 rounded transition">Edit</a>
                                        <form action="{{ route('transactions.destroyIn', $trx->id) }}" method="POST" onsubmit="return confirm('Hapus data barang masuk ini? Stok akan otomatis dikurangi kembali.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:bg-red-100 bg-red-50 px-2 py-1 rounded transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>