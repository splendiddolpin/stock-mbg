<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            📅 {{ __('Manajemen Target Harian & Libur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-r-lg shadow-sm">
                <h3 class="font-bold text-indigo-800 text-lg">Periode Aktif: {{ $period->name }}</h3>
                <p class="text-sm text-indigo-600 mt-1">Rentang: {{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}</p>
                <p class="text-xs text-indigo-500 mt-2 italic">* Rapelan Posyandu (Senin & Kamis) sudah dihitung otomatis. Anda bisa menyesuaikan porsi atau meliburkan penerima tertentu secara manual di bawah ini.</p>
            </div>

            @php
                $firstDate = $dailyTargets->keys()->first(); // Ambil tanggal pertama sebagai tab default
            @endphp

            <div x-data="{ activeTab: '{{ $firstDate }}' }" class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                
                <div class="flex overflow-x-auto bg-gray-50 border-b border-gray-200 custom-scrollbar p-2 gap-2">
                    @foreach($dailyTargets as $date => $targets)
                        @php
                            $carbonDate = \Carbon\Carbon::parse($date);
                            $isSunday = $carbonDate->isSunday();
                        @endphp
                        <button @click="activeTab = '{{ $date }}'" 
                                :class="{ 'bg-indigo-600 text-white shadow-md': activeTab === '{{ $date }}', 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100': activeTab !== '{{ $date }}' }"
                                class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-bold transition-all flex flex-col items-center min-w-[100px] {{ $isSunday ? 'opacity-70' : '' }}">
                            <span class="text-xs font-normal uppercase">{{ $carbonDate->translatedFormat('l') }}</span>
                            <span>{{ $carbonDate->format('d M') }}</span>
                            @if($isSunday) <span class="text-[10px] bg-red-100 text-red-600 px-1 rounded mt-1">Minggu</span> @endif
                        </button>
                    @endforeach
                </div>

                <div class="p-0">
                    <form action="{{ route('daily-targets.update') }}" method="POST">
                        @csrf
                        
                        @foreach($dailyTargets as $date => $targets)
                            <div x-show="activeTab === '{{ $date }}'" x-cloak>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-800 text-white text-xs uppercase tracking-wider">
                                            <tr>
                                                <th class="px-4 py-3">Nama Penerima</th>
                                                <th class="px-4 py-3 text-center">Tipe</th>
                                                <th class="px-2 py-3 text-center">Porsi B</th>
                                                <th class="px-2 py-3 text-center">Porsi K</th>
                                                <th class="px-2 py-3 text-center">Balita</th>
                                                <th class="px-2 py-3 text-center">Bumil</th>
                                                <th class="px-4 py-3 text-center bg-red-600">Liburkan?</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 text-gray-700 bg-white">
                                            @foreach($targets as $t)
                                                @php $pm = $t->beneficiary; @endphp
                                                <tr class="hover:bg-indigo-50 transition-colors {{ $t->is_holiday ? 'bg-red-50/50' : '' }}">
                                                    <td class="px-4 py-3 font-bold">{{ $pm->school_name }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $pm->type === 'posyandu' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                                                            {{ $pm->type }}
                                                        </span>
                                                    </td>
                                                    <td class="px-2 py-2"><input type="number" name="targets[{{ $t->id }}][porsi_besar]" value="{{ $t->porsi_besar }}" class="w-16 h-8 text-sm text-center border-gray-300 rounded focus:ring-indigo-500" min="0"></td>
                                                    <td class="px-2 py-2"><input type="number" name="targets[{{ $t->id }}][porsi_kecil]" value="{{ $t->porsi_kecil }}" class="w-16 h-8 text-sm text-center border-gray-300 rounded focus:ring-indigo-500" min="0"></td>
                                                    <td class="px-2 py-2"><input type="number" name="targets[{{ $t->id }}][total_balita]" value="{{ $t->total_balita }}" class="w-16 h-8 text-sm text-center border-gray-300 rounded focus:ring-indigo-500" min="0"></td>
                                                    <td class="px-2 py-2"><input type="number" name="targets[{{ $t->id }}][total_bumil_busui]" value="{{ $t->total_bumil_busui }}" class="w-16 h-8 text-sm text-center border-gray-300 rounded focus:ring-indigo-500" min="0"></td>
                                                    
                                                    <td class="px-4 py-2 text-center">
                                                        <label class="flex items-center justify-center cursor-pointer">
                                                            <input type="checkbox" name="targets[{{ $t->id }}][is_holiday]" value="1" {{ $t->is_holiday ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer">
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-4 sticky bottom-0 z-10">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan Jadwal
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>