<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Atur Jadwal Menu Mingguan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <form action="{{ route('daily-menus.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                    @csrf
                    <div class="w-full md:w-1/4">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Tanggal Sajian</label>
                        <input type="date" name="date" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Pilih Menu</label>
                        <select name="menu_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Menu dari Master --</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-gray-700">📅 Daftar Jadwal Masak</h3>
                </div>
                <div class="overflow-x-auto p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-800 text-white">
                            <tr>
                                <th class="py-3 px-4 w-1/4">Tanggal</th>
                                <th class="py-3 px-4 w-1/2">Menu yang Disajikan</th>
                                <th class="py-3 px-4 w-1/4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 font-bold {{ $schedule->date == date('Y-m-d') ? 'text-blue-600' : 'text-gray-800' }}">
                                        {{ date('d M Y', strtotime($schedule->date)) }}
                                        @if($schedule->date == date('Y-m-d'))
                                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">HARI INI</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 font-bold text-gray-900">{{ $schedule->menu->name }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('daily-menus.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 bg-red-50 px-3 py-1 rounded hover:bg-red-100 transition font-bold">Batal Jadwal</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-500 bg-gray-50/50">
                                        Belum ada jadwal menu yang dibuat.
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