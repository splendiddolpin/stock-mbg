<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-2xl">📦</span> {{ __('Gudang Arsip Excel MBG') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative flex items-center gap-3" role="alert">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="block sm:inline font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6">
                    <p class="text-gray-500 mb-6 text-sm">Menampilkan file rekapitulasi permanen yang dibekukan otomatis oleh sistem.</p>

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-bold">No</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Nama Arsip Periode</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Tanggal Dibekukan</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Ukuran</th>
                                    <th scope="col" class="px-6 py-4 font-bold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($archiveFiles as $index => $file)
                                    <tr class="bg-white hover:bg-blue-50/50 transition-colors duration-200">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-blue-700 text-base">{{ $file['display_name'] }}</div>
                                            <div class="text-[11px] text-gray-400 italic mt-1">{{ $file['filename'] }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2 text-gray-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $file['date'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full border border-gray-200">{{ $file['size'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('archives.download', $file['filename']) }}" 
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-500 hover:text-white text-sm font-bold rounded-xl transition-all duration-200 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                            <div class="text-5xl mb-4 opacity-50">📂</div>
                                            <div class="text-lg font-bold text-gray-700">Belum Ada File Arsip</div>
                                            <div class="text-sm mt-1 text-gray-400">Folder penyimpan Excel masih kosong atau robot otomatisasi belum pernah berjalan.</div>
                                        </td>
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