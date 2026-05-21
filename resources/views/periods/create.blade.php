<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buka Periode Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-sm text-blue-700">
                        <strong>Info:</strong> Anda hanya perlu memasukkan Tanggal Mulai. Sistem akan otomatis menghitung 14 hari ke depan sebagai Tanggal Berakhir untuk periode ini.
                    </p>
                </div>

                <form action="{{ route('periods.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Nama Periode</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Periode Minggu 1 & 2 April" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2 text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Buat Periode</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>