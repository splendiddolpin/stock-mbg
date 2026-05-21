<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Penerima Manfaat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center gap-3">
                <a href="{{ route('beneficiaries.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Sekolah
                </a>

                <a href="{{ route('beneficiaries.create-posyandu') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Posyandu
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 flex flex-col h-full">
                <div class="p-4 bg-white border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">🏫</span>
                        Daftar Sekolah Penerima
                    </h3>
                    <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-100">
                        {{ $beneficiaries->count() }} Sekolah
                    </span>
                </div>
                
                <div class="overflow-x-auto p-0 flex-1 max-h-[400px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50/80 text-xs text-gray-500 uppercase tracking-wider sticky top-0 z-10">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Nama Sekolah</th>
                                <th class="py-3 px-4 font-semibold text-center">Porsi</th>
                                <th class="py-3 px-4 font-semibold text-center">Alergen</th>
                                <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
    @forelse($beneficiaries as $school)
        <tbody x-data="{ open: false }" class="hover:bg-blue-50/40 transition-colors duration-200">
            <tr class="group">
                <td @click="open = !open" class="py-3 px-4 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors flex items-center gap-2">
                                {{ $school->school_name }}
                                @if($school->type === 'posyandu')
                                    <span class="text-[10px] bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full border border-pink-200">Posyandu</span>
                                @endif
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Ketuk untuk detail alergen</div>
                        </div>
                        <svg :class="{'rotate-180 text-blue-500': open, 'text-gray-300': !open}" class="w-4 h-4 transform transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </td>
                
                <td class="py-3 px-4 text-center">
                    <div class="flex flex-col items-center gap-1">
                        @if($school->type === 'posyandu')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-100 w-20 justify-center">
                                {{ $school->total_balita }} Balita
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-100 w-20 justify-center">
                                {{ $school->total_bumil_busui }} Bumil
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 w-16 justify-center">
                                {{ $school->porsi_besar }} B
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 w-16 justify-center">
                                {{ $school->porsi_kecil }} K
                            </span>
                        @endif
                    </div>
                </td>
                
                <td class="py-3 px-4 text-center">
                    @if($school->allergen_count > 0)
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 text-xs font-bold border border-red-200">
                            {{ $school->allergen_count }}
                        </span>
                    @else
                        <span class="text-gray-300 font-bold">-</span>
                    @endif
                </td>

                <td class="py-3 px-4 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('beneficiaries.edit', $school->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded text-xs font-bold transition-colors">Edit</a>
                        <form action="{{ route('beneficiaries.destroy', $school->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penerima ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded text-xs font-bold transition-colors">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            
            <tr x-show="open" x-collapse x-cloak>
                <td colspan="4" class="p-0 border-t-0">
                    <div class="bg-red-50/50 px-4 py-3 text-xs text-red-800 border-l-2 border-red-400 m-2 rounded-r-md">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <span class="font-bold block mb-0.5">Detail Alergen ({{ $school->allergen_count }} Orang):</span>
                                <span class="text-red-600/90 leading-relaxed">{{ $school->allergen_details ?: 'Belum ada catatan alergen spesifik.' }}</span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    @empty
        <tr>
            <td colspan="4" class="py-8 px-4 text-center text-gray-500">
                Belum ada data sekolah atau posyandu penerima.
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