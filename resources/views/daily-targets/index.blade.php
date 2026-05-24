<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-orange-100 text-orange-600 p-2 rounded-lg">🗓️</span>
                {{ __('Sesuaikan Porsi Harian') }}
            </h2>
            <div class="bg-white px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 shadow-sm flex items-center gap-2">
                Periode Aktif: <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs">{{ $activePeriod->name }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl shadow-sm font-medium flex items-center gap-2">
                    ✅ <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl p-4">
                <h3 class="font-bold text-gray-700 mb-3 px-1">1. Pilih Tanggal pada Kalender:</h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
                    @foreach($calendarData as $day)
                        <a href="{{ route('daily-targets.index', ['date' => $day['date']]) }}" 
                           class="block border rounded-xl flex flex-col overflow-hidden bg-white transition-all transform hover:-translate-y-1 hover:shadow-md 
                           {{ $day['is_selected'] ? 'border-orange-500 ring-2 ring-orange-200 shadow-md scale-105' : 'border-gray-200' }}">
                            
                            <div class="text-center py-2 {{ $day['is_sunday'] ? 'bg-red-500 text-white' : ($day['is_selected'] ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 border-b border-gray-200') }}">
                                <div class="text-[10px] font-bold uppercase tracking-wider opacity-90">{{ $day['day_name'] }}</div>
                                <div class="text-2xl font-black leading-none my-1">{{ $day['day_num'] }}</div>
                                <div class="text-[10px] font-semibold opacity-90">{{ $day['month'] }}</div>
                            </div>
                            
                            <div class="p-2 text-center bg-white flex-1 flex flex-col justify-center">
                                @if($day['is_sunday'])
                                    <span class="inline-block bg-red-100 text-red-600 text-[10px] font-black px-2 py-0.5 rounded">LIBUR</span>
                                @elseif($day['libur_count'] > 0)
                                    <span class="inline-block bg-red-50 border border-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded">
                                        {{ $day['libur_count'] }} Titik Libur
                                    </span>
                                @else
                                    <span class="inline-block text-gray-400 text-[10px] font-medium">Normal</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-200 sm:rounded-2xl">
                <div class="p-4 bg-orange-50/50 border-b border-orange-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">2. Sesuaikan Porsi: <span class="text-orange-600">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</span></h3>
                        <p class="text-xs text-gray-500 mt-1">Centang "Libur" jika sekolah/posyandu tersebut libur atau tidak menerima porsi di tanggal ini.</p>
                    </div>
                </div>

                <form action="{{ route('daily-targets.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $selectedDate }}">

                    <div class="overflow-x-auto p-0">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-800 text-white text-xs uppercase tracking-wider font-bold">
                                <tr>
                                    <th class="py-3 px-4 w-1/4">Penerima Manfaat</th>
                                    <th class="py-3 px-4 text-center">Status Libur</th>
                                    <th class="py-3 px-4 text-center">Porsi Besar<br><span class="text-[10px] text-gray-300 normal-case">(Siswa 4-6 / Bumil)</span></th>
                                    <th class="py-3 px-4 text-center">Porsi Kecil<br><span class="text-[10px] text-gray-300 normal-case">(Siswa 1-3 / Balita)</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                @foreach($targets as $target)
                                    <tr class="hover:bg-gray-50/80 transition-colors {{ $target->is_holiday ? 'bg-red-50/40 opacity-80' : '' }}">
                                        
                                        <td class="py-3 px-4 font-bold text-gray-900">
                                            {{ $target->beneficiary->school_name }}
                                            @if($target->beneficiary->type === 'posyandu')
                                                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] bg-pink-100 text-pink-700">Posyandu</span>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-4 text-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="targets[{{ $target->id }}][is_holiday]" value="1" {{ $target->is_holiday ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 transition">
                                                <span class="ml-2 font-bold text-red-600 text-xs uppercase">Libur</span>
                                            </label>
                                        </td>

                                        <td class="py-3 px-4 text-center">
                                            @if($target->beneficiary->type === 'sekolah')
                                                <input type="number" min="0" name="targets[{{ $target->id }}][porsi_besar]" value="{{ $target->porsi_besar }}" class="w-20 text-center border-blue-300 rounded font-bold text-blue-700 focus:ring-blue-500 shadow-sm">
                                                <input type="hidden" name="targets[{{ $target->id }}][total_bumil_busui]" value="{{ $target->total_bumil_busui }}">
                                            @else
                                                <input type="number" min="0" name="targets[{{ $target->id }}][total_bumil_busui]" value="{{ $target->total_bumil_busui }}" class="w-20 text-center border-blue-300 rounded font-bold text-blue-700 focus:ring-blue-500 shadow-sm">
                                                <input type="hidden" name="targets[{{ $target->id }}][porsi_besar]" value="{{ $target->porsi_besar }}">
                                            @endif
                                        </td>

                                        <td class="py-3 px-4 text-center">
                                            @if($target->beneficiary->type === 'sekolah')
                                                <input type="number" min="0" name="targets[{{ $target->id }}][porsi_kecil]" value="{{ $target->porsi_kecil }}" class="w-20 text-center border-emerald-300 rounded font-bold text-emerald-700 focus:ring-emerald-500 shadow-sm">
                                                <input type="hidden" name="targets[{{ $target->id }}][total_balita]" value="{{ $target->total_balita }}">
                                            @else
                                                <input type="number" min="0" name="targets[{{ $target->id }}][total_balita]" value="{{ $target->total_balita }}" class="w-20 text-center border-emerald-300 rounded font-bold text-emerald-700 focus:ring-emerald-500 shadow-sm">
                                                <input type="hidden" name="targets[{{ $target->id }}][porsi_kecil]" value="{{ $target->porsi_kecil }}">
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-gray-50 flex justify-end border-t border-gray-200 rounded-b-2xl">
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 px-8 rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan Porsi
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>