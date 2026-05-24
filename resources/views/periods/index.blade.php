<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">📅</span>
                {{ __('Kelola Periode Kegiatan MBG') }}
            </h2>
            <a href="{{ route('periods.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition-all flex items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buka Periode Baru (14 Hari)
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-medium flex items-center gap-2">
                    ✅ <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm font-medium flex items-center gap-2">
                    ⚠️ <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">Sejarah / Arsip Buku Periode</h3>
                </div>
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider font-bold">
                            <tr>
                                <th class="py-3.5 px-6">Nama Periode</th>
                                <th class="py-3.5 px-6 text-center">Tanggal Mulai</th>
                                <th class="py-3.5 px-6 text-center">Tanggal Berakhir</th>
                                <th class="py-3.5 px-6 text-center">Status Buku</th>
                                <th class="py-3.5 px-6 text-center">Aksi Pengelolaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($periods as $p)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900 text-base">
                                        {{ $p->name }}
                                    </td>
                                    <td class="py-4 px-6 text-center font-medium">
                                        {{ \Carbon\Carbon::parse($p->start_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-center font-medium">
                                        {{ \Carbon\Carbon::parse($p->end_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($p->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                                                🟢 Berjalan (Aktif)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                🔒 Sudah Tutup Buku
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex justify-center items-center gap-3">
                                            @if($p->is_active)
                                                <form action="{{ route('periods.close') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan TUTUP BUKU pada periode berjalan ini?');">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200 px-3 py-1.5 rounded-lg hover:bg-orange-600 hover:text-white transition-all shadow-sm">
                                                        Tutup Buku
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('periods.destroy', $p->id) }}" method="POST" onsubmit="return confirm('PERHATIAN: Menghapus sejarah periode akan menghapus arsip target porsi harian pada tanggal tersebut. Lanjutkan?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-bold bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                        Hapus Arsip
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <p class="text-lg font-bold text-gray-500">Belum Ada Riwayat Periode</p>
                                            <p class="text-sm">Klik tombol "Buka Periode Baru" di kanan atas untuk memulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>