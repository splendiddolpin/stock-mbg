<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Manajemen MBG</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .glass-nav {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            }
            .blob-1 { animation: blob-bounce 10s infinite ease-in-out alternate; }
            .blob-2 { animation: blob-bounce 12s infinite ease-in-out alternate-reverse; animation-delay: 2s; }
            @keyframes blob-bounce {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
        </style>
    </head>
    <body class="bg-slate-50 text-slate-900 antialiased overflow-x-hidden relative selection:bg-blue-600 selection:text-white">

        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="blob-1 absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
            <div class="blob-2 absolute top-[20%] right-[-10%] w-96 h-96 bg-emerald-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
            <div class="blob-1 absolute bottom-[-20%] left-[20%] w-96 h-96 bg-indigo-400/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70"></div>
        </div>

        <header class="fixed top-0 w-full z-50 glass-nav transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl shadow-lg flex items-center justify-center text-white font-black text-xl">
                            🍲
                        </div>
                        <span class="font-black text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-900 to-indigo-800">
                            MBG<span class="text-blue-600">Core.</span>
                        </span>
                    </div>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="group relative px-6 py-2.5 font-bold text-white rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-xl transition-all duration-300 active:scale-95">
                                    Masuk Dashboard
                                    <span class="absolute inset-0 w-full h-full rounded-full ring-2 ring-blue-600 ring-offset-2 ring-offset-slate-50 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="font-bold text-slate-600 hover:text-blue-600 transition-colors px-4 py-2">
                                    Log in
                                </a>
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <main class="relative z-10 pt-32 pb-16 lg:pt-48 lg:pb-24 min-h-screen flex flex-col justify-center">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
                
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/60 border border-blue-100 shadow-sm mb-8 animate-fade-in-up">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-bold text-blue-900 tracking-wide">Sistem Informasi v1.0 Enterprise</span>
                </div>

                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 tracking-tight leading-tight mb-6">
                    Manajemen Gizi & Logistik <br class="hidden lg:block" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-500">Secara Presisi & Cerdas.</span>
                </h1>

                <p class="mt-4 text-lg lg:text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed mb-10 font-medium">
                    Platform digital terpadu untuk mengelola rantai pasok dapur, penjadwalan gizi otomatis, dan distribusi makanan bergizi bagi penerima manfaat secara transparan, akurat, dan efisien.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 text-lg font-black text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-2">
                            Akses Dashboard Sekarang
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 text-lg font-black text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 hover:-translate-y-1 active:translate-y-0 flex items-center justify-center gap-2">
                            Mulai Gunakan Sistem
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    @endauth
                </div>

                <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                    
                    <div class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
                        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="text-3xl">📦</span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-3">Kontrol Logistik</h3>
                        <p class="text-gray-600 font-medium leading-relaxed">Kelola bahan baku masuk dan keluar dengan perhitungan gramasi presisi. Deteksi sisa stok otomatis tanpa bocor.</p>
                    </div>

                    <div class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
                        <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="text-3xl">🧑‍🍳</span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-3">Penjadwalan Gizi</h3>
                        <p class="text-gray-600 font-medium leading-relaxed">Atur resep dan jadwal menu mingguan. Sistem mendeteksi otomatis porsi sekolah dan posyandu sesuai hari libur.</p>
                    </div>

                    <div class="bg-white/70 backdrop-blur-sm p-8 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
                        <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="text-3xl">📈</span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-3">Laporan Excel Pintar</h3>
                        <p class="text-gray-600 font-medium leading-relaxed">Tutup buku periode dengan sekali klik. Sistem menghasilkan laporan rekapitulasi data siap cetak untuk pelaporan pusat.</p>
                    </div>

                </div>

            </div>
        </main>

        <footer class="relative z-10 border-t border-gray-200/60 bg-white/50 backdrop-blur-md py-8">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm font-bold text-gray-500">
                    &copy; {{ date('Y') }} Sistem Manajemen Makan Bergizi Gratis (MBG). All rights reserved.
                </p>
                <div class="flex items-center gap-6 text-sm font-bold text-gray-400">
                    <span>Versi {{ app()->version() }}</span>
                    <span>Indonesia 🇮🇩</span>
                </div>
            </div>
        </footer>

    </body>
</html>