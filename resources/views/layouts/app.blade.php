<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel MBG') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <div class="max-w-7xl mx-auto flex flex-col md:flex-row py-6 sm:px-6 lg:px-8 gap-6">
                
                <aside class="w-full md:w-1/4 flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-4 sticky top-6 border border-gray-100">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 px-3 border-b border-gray-100 pb-2">
                            Menu Navigasi Utama
                        </div>
                        
                        <nav class="space-y-3">
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                Dashboard Pusat
                            </a>

                            <div x-data="{ open: {{ request()->routeIs('beneficiaries.*', 'periods.*', 'daily-targets.*') ? 'true' : 'false' }} }" class="rounded-xl overflow-hidden bg-white border {{ request()->routeIs('beneficiaries.*', 'periods.*', 'daily-targets.*') ? 'border-purple-200 shadow-sm' : 'border-transparent hover:border-gray-100' }}">
                                <button @click="open = !open" 
                                        class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 focus:outline-none {{ request()->routeIs('beneficiaries.*', 'periods.*', 'daily-targets.*') ? 'bg-purple-50 text-purple-800 font-bold' : 'text-gray-600 hover:bg-gray-50 font-bold' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg">🏃‍♂️</span>
                                        Divisi Aslap
                                    </div>
                                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div x-show="open" x-transition.opacity class="pl-10 pr-3 py-2 space-y-1 bg-gray-50/50 text-sm">
                                    <a href="{{ route('periods.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('periods.*') ? 'text-purple-700 font-bold bg-purple-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Buku Periode (14 Hari)
                                    </a>
                                    <a href="{{ route('beneficiaries.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('beneficiaries.*') ? 'text-purple-700 font-bold bg-purple-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Data Penerima Manfaat
                                    </a>
                                    <a href="{{ route('daily-targets.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('daily-targets.*') ? 'text-purple-700 font-bold bg-purple-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Penyesuaian Porsi (Libur)
                                    </a>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('menus.*', 'daily-menus.*', 'purchase-plan.*', 'transactions.return-create') ? 'true' : 'false' }} }" class="rounded-xl overflow-hidden bg-white border {{ request()->routeIs('menus.*', 'daily-menus.*', 'purchase-plan.*', 'transactions.return-create') ? 'border-orange-200 shadow-sm' : 'border-transparent hover:border-gray-100' }}">
                                <button @click="open = !open" 
                                        class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 focus:outline-none {{ request()->routeIs('menus.*', 'daily-menus.*', 'purchase-plan.*', 'transactions.return-create') ? 'bg-orange-50 text-orange-800 font-bold' : 'text-gray-600 hover:bg-gray-50 font-bold' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg">🧑‍🍳</span>
                                        Divisi Ahli Gizi
                                    </div>
                                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div x-show="open" x-transition.opacity class="pl-10 pr-3 py-2 space-y-1 bg-gray-50/50 text-sm">
                                    <a href="{{ route('menus.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('menus.*') ? 'text-orange-700 font-bold bg-orange-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Master Menu & Resep
                                    </a>
                                    <a href="{{ route('daily-menus.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('daily-menus.*') ? 'text-orange-700 font-bold bg-orange-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Jadwal Menu Masak
                                    </a>
                                    <a href="{{ route('purchase-plan.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('purchase-plan.*') ? 'text-orange-700 font-bold bg-orange-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Rencana Belanja (PO)
                                    </a>
                                    <a href="{{ route('transactions.return-create') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('transactions.return-create') ? 'text-emerald-700 font-bold bg-emerald-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Pengembalian Sisa (Retur)
                                    </a>
                                </div>
                            </div>

                            <div x-data="{ open: {{ request()->routeIs('items.*', 'transactions.check-order', 'transactions.in', 'transactions.in-create', 'transactions.out-create', 'transactions.recap', 'usage-recaps.*') ? 'true' : 'false' }} }" class="rounded-xl overflow-hidden bg-white border {{ request()->routeIs('items.*', 'transactions.check-order', 'transactions.in', 'transactions.in-create', 'transactions.out-create', 'transactions.recap', 'usage-recaps.*') ? 'border-emerald-200 shadow-sm' : 'border-transparent hover:border-gray-100' }}">
                                <button @click="open = !open" 
                                        class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all duration-200 focus:outline-none {{ request()->routeIs('items.*', 'transactions.check-order', 'transactions.in', 'transactions.in-create', 'transactions.out-create', 'transactions.recap', 'usage-recaps.*') ? 'bg-emerald-50 text-emerald-800 font-bold' : 'text-gray-600 hover:bg-gray-50 font-bold' }}">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg">📦</span>
                                        Divisi Logistik Gudang
                                    </div>
                                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div x-show="open" x-transition.opacity class="pl-10 pr-3 py-2 space-y-1 bg-gray-50/50 text-sm">
                                    <a href="{{ route('items.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('items.*') ? 'text-emerald-700 font-bold bg-emerald-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Master Bahan Baku
                                    </a>
                                    <a href="{{ route('transactions.check-order') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('transactions.check-order') ? 'text-emerald-700 font-bold bg-emerald-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Verifikasi PO Datang
                                    </a>
                                    <a href="{{ route('transactions.in') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('transactions.in', 'transactions.in-create') ? 'text-emerald-700 font-bold bg-emerald-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Input Masuk (Manual)
                                    </a>
                                    
                                    <a href="{{ route('transactions.out-create') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('transactions.out-create') ? 'text-rose-700 font-bold bg-rose-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Input Keluar (Manual)
                                    </a>

                                    <a href="{{ route('transactions.recap') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('transactions.recap') ? 'text-emerald-700 font-bold bg-emerald-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Rekap Barang Masuk
                                    </a>
                                    <a href="{{ route('usage-recaps.index') }}" class="block py-2 px-3 rounded-lg transition-colors {{ request()->routeIs('usage-recaps.*') ? 'text-emerald-700 font-bold bg-emerald-100/70' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                                        • Rekap Barang Keluar
                                    </a>
                                </div>
                            </div>

                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-6 mb-3 px-3 border-b border-gray-100 pb-2">
                                Administrasi & Keuangan
                            </div>
                            
                            <a href="{{ route('staff-cash.index') }}" 
                               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('staff-cash.*') ? 'bg-amber-50 text-amber-700 font-bold border border-amber-200 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                                <span class="text-lg mr-3 {{ request()->routeIs('staff-cash.*') ? '' : 'grayscale opacity-70' }}">💰</span>
                                Kas & Keuangan Staf
                            </a>

                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-6 mb-3 px-3 border-b border-gray-100 pb-2">
                                Program Interaktif
                            </div>
                            
                            <a href="{{ route('menu-catalogs.index') }}" 
                               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('menu-catalogs.*') ? 'bg-pink-50 text-pink-700 font-bold border border-pink-100' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                                <span class="text-lg mr-3 {{ request()->routeIs('menu-catalogs.*') ? '' : 'grayscale opacity-70' }}">📱</span>
                                Request Menu Siswa
                            </a>

                            <a href="{{ route('archives.index') }}" 
                               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('archives.*') ? 'bg-blue-50 text-blue-800 font-bold border border-blue-200 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                                <span class="text-lg mr-3 {{ request()->routeIs('archives.*') ? '' : 'grayscale opacity-70' }}">📁</span>
                                Gudang Arsip Excel
                            </a>
                            
                            
                        </nav>
                    </div>
                </aside>

                <main class="w-full md:w-3/4">
                    {{ $slot }}
                </main>
                
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Cari semua form di halaman yang punya atribut onsubmit berisi 'confirm'
                const confirmForms = document.querySelectorAll('form[onsubmit*="confirm"]');

                confirmForms.forEach(form => {
                    // 2. Ambil teks asli dari atribut onsubmit
                    const onsubmitValue = form.getAttribute('onsubmit');
                    let alertMessage = 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

                    // 3. Ekstrak pesan dari dalam tanda kutip menggunakan Regex sederhana
                    const match = onsubmitValue.match(/confirm\(['"](.*?)['"]\)/);
                    if (match && match[1]) {
                        alertMessage = match[1];
                    }

                    // 4. Hapus atribut onsubmit bawaan agar pop-up browser tidak muncul
                    form.removeAttribute('onsubmit');

                    // 5. Ganti dengan Event Listener baru menggunakan SweetAlert2
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); // Tahan form agar tidak langsung terkirim

                        Swal.fire({
                            title: 'Konfirmasi Tindakan',
                            text: alertMessage, // Gunakan teks asli yang dicuri dari atribut tadi
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48', // Tailwind rose-600
                            cancelButtonColor: '#94a3b8',  // Tailwind slate-400
                            confirmButtonText: 'Ya, Lanjutkan!',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'rounded-3xl border border-gray-100 shadow-xl',
                                confirmButton: 'rounded-xl font-bold px-6 py-2.5',
                                cancelButton: 'rounded-xl font-bold px-6 py-2.5 text-gray-700',
                                title: 'text-2xl font-black text-gray-800'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Jika user klik 'Ya', kirim form secara paksa
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    </body>
</html>