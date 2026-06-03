<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Susun Menu MBG Kamu!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Menggunakan 100dvh agar tidak terpotong address bar di browser HP (Chrome/Safari Mobile) */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #0f172a; 
            color: #f8fafc; 
        }
        [x-cloak] { display: none !important; }
        
        /* Scrollbar Styling yang Nyaman */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #475569; }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
    </style>
</head>
<body x-data="menuBuilder()" class="h-[100dvh] w-full flex flex-col overflow-hidden bg-[#0f172a]">

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="fixed top-16 md:top-24 left-1/2 transform -translate-x-1/2 z-50 bg-rose-500 text-white px-6 py-3 rounded-full shadow-2xl font-bold flex items-center gap-3 animate-pulse border-2 border-rose-400 w-[90%] md:w-auto text-sm md:text-base">
            <span>🛑</span> <span>{{ session('error') }}</span>
            <button @click="show = false" class="bg-rose-700 rounded-full p-1 ml-auto"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" class="fixed top-16 md:top-24 left-1/2 transform -translate-x-1/2 z-50 bg-emerald-500 text-white px-6 py-3 rounded-full shadow-2xl font-bold flex items-center gap-3 animate-bounce border-2 border-emerald-400 w-[90%] md:w-auto text-sm md:text-base">
            <span>✅</span> <span>{{ session('success') }}</span>
            <button @click="show = false" class="bg-emerald-700 rounded-full p-1 ml-auto"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <header class="flex-none bg-[#14b8a6] px-4 md:px-6 py-4 flex flex-col md:flex-row items-center justify-between shadow-md relative z-20 gap-3 md:gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto justify-center md:justify-start">
            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm hidden md:block">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div class="text-center md:text-left">
                <h1 class="font-bold text-white tracking-wide leading-tight text-[13px] md:text-base uppercase md:normal-case">SPPG Borobudur Borobudur</h1>
                <p class="text-teal-100 text-[10px] md:text-xs font-medium uppercase tracking-widest md:normal-case md:tracking-normal">Susun Menu MBG Kamu</p>
            </div>
        </div>
        
        <div class="flex bg-teal-800/40 p-1 rounded-full border border-teal-600/50 backdrop-blur-sm w-full md:w-auto justify-between md:justify-start">
            <button @click="setPorsi('kecil')" :class="porsi === 'kecil' ? 'bg-white text-teal-700 shadow-sm' : 'text-teal-50'" class="flex-1 md:flex-none px-4 md:px-5 py-1.5 md:py-1.5 rounded-full text-xs font-black transition-all uppercase tracking-widest text-center">
                🍴 Porsi Kecil
            </button>
            <button @click="setPorsi('besar')" :class="porsi === 'besar' ? 'bg-white text-teal-700 shadow-sm' : 'text-teal-50'" class="flex-1 md:flex-none px-4 md:px-5 py-1.5 md:py-1.5 rounded-full text-xs font-black transition-all uppercase tracking-widest text-center">
                🍱 Porsi Besar
            </button>
        </div>
    </header>

    <div class="flex-1 flex flex-col md:flex-row overflow-hidden min-h-0 relative">
        
        <div class="flex-1 flex flex-col overflow-hidden min-h-0 bg-[#0f172a] relative z-10">
            
            <div class="flex-none px-4 md:px-6 py-4 border-b border-slate-800 overflow-x-auto whitespace-nowrap custom-scrollbar bg-[#111827]/50">
                <div class="flex gap-2.5 md:gap-3">
                    <template x-for="cat in categories" :key="cat.id">
                        <button @click="activeCategory = cat.id" 
                                :class="activeCategory === cat.id ? 'bg-[#db2777] text-white border-[#be185d] scale-105 shadow-lg shadow-pink-500/20' : 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700'"
                                class="px-4 md:px-5 py-2 md:py-2.5 rounded-xl text-[10px] md:text-xs font-bold border flex items-center gap-1.5 transition-all transform uppercase tracking-tighter">
                            <span class="text-base md:text-lg" x-text="cat.icon"></span>
                            <span x-text="cat.id"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar min-h-0">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <h2 class="text-lg md:text-xl font-extrabold text-white flex items-center gap-2">
                        <span class="p-1.5 md:p-2 bg-slate-800 rounded-lg text-sm md:text-base" x-text="currentCategoryObj.icon"></span>
                        <span x-text="activeCategory"></span>
                    </h2>
                    <span class="text-[10px] md:text-xs text-slate-500 font-bold uppercase tracking-widest" x-text="filteredItems.length + ' Pilihan'"></span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4 pb-24 md:pb-6">
                    <template x-for="item in filteredItems" :key="item.id">
                        <div @click="toggleItem(item)" 
                             :class="isSelected(item) ? 'bg-[#831843]/40 border-[#db2777] ring-2 ring-pink-500/20' : 'bg-[#1e293b] border-transparent hover:bg-slate-700 hover:border-slate-600'"
                             class="border rounded-2xl p-3 md:p-4 cursor-pointer transition-all relative overflow-hidden group shadow-sm flex flex-col justify-between h-full min-h-[90px]">
                            
                            <div>
                                <h3 class="font-bold text-white mb-1 group-hover:text-teal-300 transition-colors text-xs md:text-sm leading-snug" x-text="item.name"></h3>
                            </div>
                            <p class="text-[11px] md:text-xs text-slate-400 font-black tracking-wide mt-1" x-text="formatRp(item.price)"></p>
                            
                            <div x-show="isSelected(item)" class="absolute bottom-2 md:bottom-3 right-2 md:right-3 text-[#db2777]" x-cloak>
                                <div class="bg-pink-500 text-white rounded-full p-1 shadow-lg">
                                    <svg class="w-3 h-3 md:w-3 md:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="md:hidden absolute bottom-0 left-0 right-0 p-4 pb-6 bg-gradient-to-t from-[#0f172a] to-transparent z-20 pointer-events-none">
                <button @click="mobileCartOpen = true" 
                        x-show="selectedItems.length > 0"
                        x-transition:enter="duration-300 ease-out" x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:leave="duration-200 ease-in" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
                        class="w-full bg-gradient-to-r from-teal-500 to-emerald-500 p-4 rounded-2xl shadow-[0_10px_40px_rgba(20,184,166,0.3)] flex justify-between items-center pointer-events-auto active:scale-95 transition-transform" x-cloak>
                    
                    <div class="text-left">
                        <p class="text-[9px] font-black text-teal-100 uppercase tracking-widest opacity-80 mb-0.5">Keranjang Menu</p>
                        <div class="text-white font-black text-sm flex items-center gap-1.5">
                            <span class="bg-white/20 px-2 py-0.5 rounded-md" x-text="selectedItems.length + ' Item'"></span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-[9px] font-black uppercase tracking-widest mb-0.5" :class="progress > 100 ? 'text-rose-200' : 'text-teal-100'" x-text="progress > 100 ? 'Overbudget!' : 'Total Biaya'"></p>
                            <h3 class="font-black text-white text-base leading-none" :class="progress > 100 ? 'text-rose-200' : ''" x-text="formatRp(totalPrice)"></h3>
                        </div>
                        <div class="bg-white text-teal-600 p-1.5 rounded-full shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg>
                        </div>
                    </div>
                </button>
            </div>

        </div>

        <div :class="mobileCartOpen ? 'translate-y-0' : 'translate-y-full md:translate-y-0'"
             class="fixed md:static inset-0 z-40 md:z-10 w-full md:w-[380px] flex-none bg-[#1e293b] flex flex-col h-[100dvh] md:h-full shadow-2xl border-l border-slate-800 overflow-hidden min-h-0 transition-transform duration-300 ease-in-out">
            
            <div class="flex-none bg-[#14b8a6] p-6 pt-10 md:pt-6 rounded-b-[2rem] md:rounded-bl-none shadow-lg z-10 relative">
                <button @click="mobileCartOpen = false" class="md:hidden absolute top-4 right-5 bg-teal-800/40 hover:bg-teal-800/60 text-white p-1.5 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h3 class="md:hidden font-black text-white text-lg mb-4 opacity-90">Keranjang Menu Kamu</h3>

                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-teal-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Total Menu</p>
                        <h2 class="text-3xl font-black text-white" x-text="formatRp(totalPrice)"></h2>
                    </div>
                    <div class="text-right">
                        <p class="text-teal-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Batas Maks</p>
                        <p class="text-sm font-black text-white" x-text="formatRp(maxBudget)"></p>
                    </div>
                </div>
                
                <div class="w-full bg-teal-950/30 rounded-full h-3 mb-2 overflow-hidden mt-4 border border-teal-400/20 relative">
                    <div class="h-full rounded-full transition-all duration-700 ease-out relative" 
                         :class="progress > 100 ? 'bg-rose-500' : 'bg-white'" 
                         :style="'width: ' + Math.min(progress, 100) + '%'">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent w-full animate-shimmer"></div>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <p class="text-[10px] font-bold" :class="progress > 100 ? 'text-rose-100 animate-pulse' : 'text-teal-50'">
                        <span x-text="progress > 100 ? '⚠️ Budget Tidak Cukup, Kurangi Makanan!' : 'Budget Terpakai: ' + Math.round(progress) + '%'"></span>
                    </p>
                    <p class="text-[10px] font-black text-white bg-teal-800/50 px-2 py-0.5 rounded" x-text="formatRp(maxBudget - totalPrice)"></p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-5 md:p-6 custom-scrollbar flex flex-col gap-6 min-h-0 pb-32 md:pb-6">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-white text-xs md:text-sm uppercase tracking-widest opacity-60">Menu Terpilih</h3>
                        <button @click="selectedItems = []" x-show="selectedItems.length > 0" class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors uppercase">Hapus Semua</button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(item, index) in selectedItems" :key="index">
                            <div class="bg-slate-800/50 p-3 rounded-xl flex items-center justify-between border border-slate-700/50 group">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg md:text-xl" x-text="getCategoryIcon(item.category)"></span>
                                    <div>
                                        <p class="text-xs md:text-sm font-bold text-white leading-tight" x-text="item.name"></p>
                                        <p class="text-[10px] text-teal-400 font-black mt-0.5 uppercase tracking-tighter" x-text="formatRp(item.price)"></p>
                                    </div>
                                </div>
                                <button @click="toggleItem(item)" class="text-slate-500 hover:text-rose-500 p-1 transition-colors">
                                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <div x-show="selectedItems.length === 0" class="text-center py-10 text-slate-600 text-[11px] font-bold border-2 border-dashed border-slate-800 rounded-2xl uppercase tracking-widest">
                            Keranjang masih kosong 😋
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-6 border-t border-slate-800">
                    <h3 class="font-bold text-slate-500 text-[10px] uppercase tracking-widest mb-4">Ringkasan Gizi Seimbang</h3>
                    <div class="grid grid-cols-1 gap-2.5 md:gap-3">
                        <template x-for="(data, cat) in summary" :key="cat">
                            <div class="flex justify-between items-center text-xs" x-show="data.count > 0">
                                <div class="flex items-center gap-2 text-slate-300 font-medium">
                                    <span x-text="data.icon"></span>
                                    <span x-text="cat"></span>
                                </div>
                                <span class="font-black text-white" x-text="formatRp(data.total)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex-none p-5 md:p-6 border-t border-slate-800 bg-[#1e293b] absolute md:static bottom-0 left-0 w-full z-20">
                <button @click="openSubmitModal()" 
                        :disabled="selectedItems.length === 0 || progress > 100"
                        :class="selectedItems.length === 0 || progress > 100 ? 'bg-slate-700 text-slate-500 cursor-not-allowed opacity-50' : 'bg-[#14b8a6] hover:bg-teal-400 text-white shadow-xl shadow-teal-500/10 active:scale-95'"
                        class="w-full py-3.5 md:py-4 rounded-2xl font-black text-xs md:text-sm uppercase tracking-widest transition-all flex justify-center items-center gap-2">
                    <span>🚀</span>
                    Kirim Request Menu
                </button>
            </div>
        </div>

        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" x-transition.opacity x-cloak>
            <div @click.away="showModal = false" class="bg-white text-slate-900 rounded-[2rem] md:rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all flex flex-col max-h-[90vh]" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                <div class="flex-none bg-gradient-to-r from-teal-500 to-emerald-500 p-6 md:p-8 text-center relative">
                    <div class="text-4xl md:text-5xl mb-3 md:mb-4">🥳</div>
                    <h2 class="text-xl md:text-2xl font-black text-white drop-shadow-md leading-tight">Sedikit Lagi Menumu Siap!</h2>
                    <p class="text-teal-50 font-medium mt-1 text-xs md:text-sm">Kasih tahu koki siapa namamu dan asal sekolahmu.</p>
                </div>
                
                <form action="{{ route('request-menu.store') }}" method="POST" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-4 md:space-y-5 custom-scrollbar">
                    @csrf
                    <input type="hidden" name="menu_name" :value="selectedItems.map(i => i.name).join(', ')">
                    
                    <div>
                        <label class="block text-[10px] md:text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5 md:mb-2">Asal Sekolah / Lokasimu</label>
                        <select name="beneficiary_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl md:rounded-2xl px-4 md:px-5 py-3 md:py-3.5 focus:ring-4 focus:ring-teal-100 focus:border-teal-500 font-bold text-sm transition-all outline-none" required>
                            <option value="">Pilih Sekolah...</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->school_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] md:text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5 md:mb-2">Nama Lengkap / Kelas</label>
                        <input type="text" name="student_name" placeholder="Misal: Budi Santoso (6-B)" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl md:rounded-2xl px-4 md:px-5 py-3 md:py-3.5 focus:ring-4 focus:ring-teal-100 focus:border-teal-500 font-bold text-sm transition-all outline-none" required>
                    </div>

                    <div>
                        <label class="block text-[10px] md:text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5 md:mb-2">Kenapa Milih Menu Ini?</label>
                        <textarea name="reason" rows="2" placeholder="Tulis alasanmu di sini (Opsional)..." class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl md:rounded-2xl px-4 md:px-5 py-3 md:py-3.5 focus:ring-4 focus:ring-teal-100 focus:border-teal-500 font-bold text-sm transition-all outline-none resize-none"></textarea>
                    </div>

                    <div class="pt-2 md:pt-4 flex gap-3">
                        <button type="button" @click="showModal = false" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black py-3 md:py-4 rounded-xl md:rounded-2xl transition-all uppercase text-[10px] md:text-xs tracking-widest">Batal</button>
                        <button type="submit" class="flex-1 bg-teal-500 hover:bg-teal-600 text-white font-black py-3 md:py-4 rounded-xl md:rounded-2xl shadow-xl shadow-teal-500/20 transition-all active:scale-95 uppercase text-[10px] md:text-xs tracking-widest">Kirim Sekarang!</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('menuBuilder', () => ({
                porsi: 'besar',
                maxBudget: 10000,
                activeCategory: 'Karbohidrat',
                showModal: false,
                mobileCartOpen: false, // STATE BARU UNTUK MOBILE KERANJANG
                
                categories: [
                    { id: 'Karbohidrat', icon: '🍚' },
                    { id: 'Protein Hewani', icon: '🍗' },
                    { id: 'Protein Nabati', icon: '🧆' },
                    { id: 'Sayur', icon: '🥬' },
                    { id: 'Buah', icon: '🍎' },
                    { id: 'Susu', icon: '🥛' },
                ],
                
                items: {!! $items->map(fn($i) => [
                    'id' => $i->id,
                    'category' => $i->category,
                    'name' => $i->name,
                    'price' => (int)$i->price
                ])->toJson() !!},

                selectedItems: [],

                setPorsi(jenis) {
                    this.porsi = jenis;
                    this.maxBudget = jenis === 'kecil' ? 8000 : 10000;
                },

                get currentCategoryObj() {
                    return this.categories.find(c => c.id === this.activeCategory) || {};
                },

                get filteredItems() {
                    return this.items.filter(i => i.category === this.activeCategory);
                },

                get totalPrice() {
                    return this.selectedItems.reduce((sum, item) => sum + item.price, 0);
                },

                get progress() {
                    return (this.totalPrice / this.maxBudget) * 100;
                },

                isSelected(item) {
                    return this.selectedItems.some(i => i.id === item.id);
                },

                toggleItem(item) {
                    const index = this.selectedItems.findIndex(i => i.id === item.id);
                    if (index > -1) {
                        this.selectedItems.splice(index, 1);
                        // Auto close mobile cart jika kosong
                        if(this.selectedItems.length === 0) this.mobileCartOpen = false;
                    } else {
                        this.selectedItems.push(item);
                    }
                },

                getCategoryIcon(catName) {
                    const cat = this.categories.find(c => c.id === catName);
                    return cat ? cat.icon : '🍽️';
                },

                get summary() {
                    let sum = {};
                    this.categories.forEach(c => sum[c.id] = { count: 0, total: 0, icon: c.icon });
                    this.selectedItems.forEach(item => {
                        if(sum[item.category]) {
                            sum[item.category].count++;
                            sum[item.category].total += item.price;
                        }
                    });
                    return sum;
                },

                formatRp(angka) {
                    return 'Rp ' + angka.toLocaleString('id-ID');
                },

                openSubmitModal() {
                    if(this.selectedItems.length > 0 && this.progress <= 100) {
                        this.showModal = true;
                    }
                }
            }));
        });
    </script>
</body>
</html>