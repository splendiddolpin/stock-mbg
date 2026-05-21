<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sticky top-6">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-3">
                            Menu Navigasi
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                Dashboard
                            </a>

                            <a href="{{ route('beneficiaries.index') }}" 
                               class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('beneficiaries.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                Penerima Manfaat
                            </a>
                            
                            <a href="{{ route('transactions.in') }}" 
                               class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('transactions.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                Barang Masuk
                            </a>
                            
                            <a href="{{ route('transactions.recap') }}" 
                               class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('transactions.recap') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Rekap Masuk
                            </a>

                            <a href="{{ route('items.index') }}" 
                                class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('items.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    Master Bahan
                            </a>

                            <a href="{{ route('menus.index') }}" 
                                class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('menus.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Master Menu
                            </a>
                            
                            <a href="{{ route('periods.create') }}" 
                               class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('periods.create') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tambah Periode
                            </a>

                            <a href="{{ route('daily-menus.index') }}" 
                                class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('daily-menus.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Jadwal Menu (Kalender)
                            </a>

                            <a href="{{ route('usage-recaps.index') }}" 
                                class="flex items-center px-3 py-2 rounded-md transition-colors {{ request()->routeIs('usage-recaps.*') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                Rekap Penggunaan
                            </a>
                            
                        </nav>
                    </div>
                </aside>

                <main class="w-full md:w-3/4">
                    {{ $slot }}
                </main>
                
            </div>
        </div>
    </body>
</html>