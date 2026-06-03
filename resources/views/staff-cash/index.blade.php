<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="bg-amber-100 text-amber-600 p-2 rounded-lg">💰</span>
            {{ __('Buku Kas Operasional Staf') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-bold flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-6 text-white shadow-lg">
                    <p class="text-amber-100 font-black text-sm uppercase tracking-widest mb-1">Saldo Kas Saat Ini</p>
                    <h3 class="text-4xl font-black">Rp {{ number_format($balance, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-center">
                    <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-1">Total Pemasukan (In)</p>
                    <h3 class="text-2xl font-black text-emerald-500">+ Rp {{ number_format($totalIn, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-center">
                    <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-1">Total Pengeluaran (Out)</p>
                    <h3 class="text-2xl font-black text-rose-500">- Rp {{ number_format($totalOut, 0, ',', '.') }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 lg:col-span-1 sticky top-6">
                    <h3 class="font-black text-gray-800 text-lg mb-4 border-b pb-2">Catat Transaksi Baru</h3>
                    <form action="{{ route('staff-cash.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">Jenis Arus Kas</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="in" class="peer sr-only" required>
                                    <div class="text-center py-2 rounded-xl border border-gray-200 text-sm font-bold text-gray-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:border-emerald-500 transition-all">Pemasukan (In)</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="out" class="peer sr-only">
                                    <div class="text-center py-2 rounded-xl border border-gray-200 text-sm font-bold text-gray-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 peer-checked:border-rose-500 transition-all">Pengeluaran (Out)</div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">Nominal (Rp)</label>
                            <input type="number" name="amount" placeholder="Misal: 50000" class="w-full rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500 font-bold" min="1" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase tracking-wider">Keterangan</label>
                            <textarea name="description" rows="2" placeholder="Misal: Beli gas LPG cadangan" class="w-full rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500 text-sm resize-none" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-3 rounded-xl shadow-md transition-all active:scale-95">
                            Simpan Transaksi
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden lg:col-span-2">
                    <div class="p-4 bg-gray-50 border-b border-gray-100">
                        <h3 class="font-black text-gray-700">Riwayat Arus Kas</h3>
                    </div>
                    <div class="overflow-x-auto p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white text-gray-400 text-[10px] uppercase tracking-widest border-b">
                                <tr>
                                    <th class="py-3 px-5">Tanggal</th>
                                    <th class="py-3 px-5">Keterangan</th>
                                    <th class="py-3 px-5 text-right">Nominal</th>
                                    <th class="py-3 px-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($transactions as $trx)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-5 font-bold text-gray-600 whitespace-nowrap">{{ date('d M Y', strtotime($trx->date)) }}</td>
                                        <td class="py-3 px-5 text-gray-800 font-medium">{{ $trx->description }}</td>
                                        <td class="py-3 px-5 text-right font-black {{ $trx->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $trx->type === 'in' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-5 text-center">
                                            <form action="{{ route('staff-cash.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Hapus catatan transaksi ini? Saldo akan dihitung ulang secara otomatis.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-600 bg-rose-50 hover:bg-rose-100 p-1.5 rounded transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-gray-400 font-medium italic">Belum ada transaksi kas yang dicatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>