<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                📦 {{ __('Daftar Barang Masuk') }}
            </h2>

            <form action="" method="GET" class="flex items-center gap-3">
                <label class="text-sm font-bold text-gray-700 hidden md:block">Fokus Periode:</label>
                <select name="period_id" onchange="this.form.submit()" class="rounded-xl border-gray-300 shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 font-semibold text-blue-700 bg-blue-50 cursor-pointer">
                    @foreach($allPeriods as $p)
                        <option value="{{ $p->id }}" {{ ($activePeriod->id ?? 0) == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} {{ $p->is_active ? '🔥 (Aktif Hari Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex justify-end">
                <a href="{{ route('transactions.createIn') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-all">+ Input Barang Masuk</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase border-b border-gray-200">
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
                        @forelse($transactions as $trx)
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
                                        <a href="{{ route('transactions.editIn', $trx->id) }}" class="text-indigo-600 hover:bg-indigo-100 bg-indigo-50 px-3 py-1.5 rounded-lg font-semibold transition-colors">Edit</a>
                                        <form action="{{ route('transactions.destroyIn', $trx->id) }}" method="POST" onsubmit="return confirm('Hapus data barang masuk ini? Stok akan otomatis dikurangi kembali.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:bg-red-100 bg-red-50 px-3 py-1.5 rounded-lg font-semibold transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <div class="text-4xl mb-2">📦</div>
                                    <p class="font-bold">Belum Ada Barang Masuk</p>
                                    <p class="text-xs">Tidak ada riwayat input barang masuk pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>