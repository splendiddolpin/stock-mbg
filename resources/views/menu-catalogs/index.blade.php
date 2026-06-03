<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span>📱</span> {{ __('Program Request Menu Siswa') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10 print:py-0" x-data="{ tab: 'statistik' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-bold flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="flex space-x-2 bg-white p-1.5 rounded-2xl shadow-sm border border-gray-200 w-full md:w-max">
                <button @click="tab = 'statistik'" 
                        :class="tab === 'statistik' ? 'bg-rose-100 text-rose-700 shadow-sm ring-1 ring-rose-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                    📊 Statistik Suara Siswa
                    <span class="bg-rose-600 text-white text-[10px] px-2 py-0.5 rounded-full">{{ number_format($totalVotes) }}</span>
                </button>
                
                <button @click="tab = 'katalog'" 
                        :class="tab === 'katalog' ? 'bg-indigo-100 text-indigo-700 shadow-sm ring-1 ring-indigo-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                    📖 Kelola Katalog Aplikasi
                </button>
            </div>

            <div x-show="tab === 'statistik'" x-transition.opacity class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                    <div class="p-5 bg-slate-800 text-white flex items-center gap-2">
                        <span class="text-xl">🏆</span>
                        <h3 class="font-black text-sm uppercase tracking-wider">Rangking Menu Paling Diinginkan Siswa</h3>
                    </div>
                    
                    <div class="p-6">
                        @if($rankedRequests->isEmpty())
                            <div class="text-center py-12 text-gray-400 italic font-medium">Belum ada voting request dari siswa yang masuk.</div>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider border-b">
                                        <tr>
                                            <th class="py-3.5 px-5 w-12 text-center">No</th>
                                            <th class="py-3.5 px-5 w-1/4">Nama Makanan / Minuman</th>
                                            <th class="py-3.5 px-5">Tren Popularitas</th>
                                            <th class="py-3.5 px-5 text-center w-32">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-gray-700">
                                        @php $maxVote = $rankedRequests->max('total_request') ?: 1; @endphp
                                        @foreach($rankedRequests as $index => $req)
                                            @php $percentage = ($req->total_request / $maxVote) * 100; @endphp
                                            <tr class="hover:bg-slate-50/60 transition-colors">
                                                <td class="py-4 px-5 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                                                <td class="py-4 px-5 font-black text-gray-900">{{ $req->menu_name }}</td>
                                                <td class="py-4 px-5">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-full bg-slate-100 h-6 rounded-lg overflow-hidden relative border border-slate-200/50">
                                                            <div class="h-full bg-gradient-to-r from-pink-500 to-rose-500" style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                        <span class="text-xs font-bold text-rose-500 w-8">{{ round($percentage) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-5 text-center font-black text-lg text-slate-800 bg-slate-50/40">
                                                    {{ number_format($req->total_request) }} 👤
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                    <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-700 flex items-center gap-2">
                        <span>🕒</span> Log Suara Siswa Terbaru (Real-time)
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4">Waktu</th>
                                    <th class="py-3 px-4">Nama Siswa</th>
                                    <th class="py-3 px-4">Asal Sekolah</th>
                                    <th class="py-3 px-4">Menu Pilihan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                @forelse($latestRequests as $log)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-3 px-4 text-xs text-gray-400 font-medium whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                                        <td class="py-3 px-4 font-bold text-gray-800">{{ $log->student_name }}</td>
                                        <td class="py-3 px-4"><span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold border border-blue-100">{{ $log->beneficiary->school_name }}</span></td>
                                        <td class="py-3 px-4 font-black text-pink-600 text-sm">✨ {{ $log->menu_name }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-6 text-center text-gray-400 italic">Belum ada aktivitas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'katalog'" x-transition.opacity style="display: none;" x-data="{ search: '' }">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200">
                    <div class="p-5 bg-indigo-50 border-b border-indigo-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="font-black text-indigo-900 text-lg">Kelola Data Makanan</h3>
                            <p class="text-sm text-indigo-700 mt-1">Data yang diubah di sini akan langsung tampil di HP siswa.</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <div class="relative w-full sm:w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input x-model="search" type="text" placeholder="Cari menu atau kategori..." class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-indigo-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm shadow-sm transition-all outline-none">
                                <button x-show="search !== ''" @click="search = ''" style="display: none;" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-rose-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <a href="{{ route('menu-catalogs.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-all text-sm flex items-center justify-center gap-2 whitespace-nowrap">
                                ➕ Tambah Menu
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="py-3 px-4">Kategori</th>
                                        <th class="py-3 px-4">Nama Menu</th>
                                        <th class="py-3 px-4 text-center">Harga Default (Rp)</th>
                                        <th class="py-3 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($catalogs as $item)
                                        <tr class="hover:bg-gray-50 transition-colors"
                                            x-show="search === '' || '{{ strtolower(addslashes($item->name)) }}'.includes(search.toLowerCase()) || '{{ strtolower(addslashes($item->category)) }}'.includes(search.toLowerCase())">
                                            
                                            <td class="py-3 px-4 font-bold text-indigo-600">{{ $item->category }}</td>
                                            <td class="py-3 px-4 font-black text-gray-900">{{ $item->name }}</td>
                                            <td class="py-3 px-4 text-center font-bold text-gray-600">{{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex justify-center gap-2">
                                                    <a href="{{ route('menu-catalogs.edit', $item->id) }}" class="bg-amber-100 text-amber-700 hover:bg-amber-200 px-3 py-1 rounded-lg font-bold text-xs transition-colors">Edit</a>
                                                    <form action="{{ route('menu-catalogs.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini dari HP siswa?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-lg font-bold text-xs transition-colors">Hapus</button>
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
            </div>

        </div>
    </div>
</x-app-layout>