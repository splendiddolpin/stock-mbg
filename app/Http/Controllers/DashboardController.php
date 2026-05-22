<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Beneficiary;
use App\Models\Menu;
use App\Models\DailyMenu;
use App\Models\Period;
use App\Models\DailyTarget;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data master
        $items = Item::orderBy('name', 'asc')->get();
        $beneficiaries = Beneficiary::orderBy('school_name', 'asc')->get();
        
        // 2. Hitung statistik penerima manfaat (Sekolah vs Posyandu)
        $totalSchools = $beneficiaries->where('type', 'sekolah')->count();
        $totalPosyandu = $beneficiaries->where('type', 'posyandu')->count();
        
        $totalStudents = $beneficiaries->sum('total_students');
        $totalJiwaPosyandu = $beneficiaries->sum('total_balita') + $beneficiaries->sum('total_bumil_busui');
        
        $lowStockCount = $items->whereIn('status', ['Habis', 'Hampir Habis'])->count();

        // Cari Periode Aktif (Dipindah ke atas agar bisa dipakai oleh kalkulator & kalender)
        $activePeriod = Period::where('is_active', true)->latest()->first();

        // 3. Ambil jadwal menu untuk HARI INI
        $hariIni = now()->toDateString();
        $jadwalHariIni = DailyMenu::with('menu.items')
                        ->where('date', $hariIni)
                        ->first();

        // =========================================================================
        // 4. LOGIKA KEBUTUHAN BELANJA BESOK (SINKRON DENGAN KALENDER LIBUR/RAPELAN)
        // =========================================================================
        $besok = now()->addDay()->toDateString();
        $jadwalBesokList = DailyMenu::with('menu.items')->where('date', $besok)->get();
        
        $kebutuhanBesok = [];
        $totalBiayaBesok = 0;
        $kebutuhanItems = [];

        // WADAH TARGET MURNI HARI ESOK
        $porsiSekolahBesar = 0;
        $porsiSekolahKecil = 0;
        $porsiPosyanduBesar = 0; // Untuk Bumil
        $porsiPosyanduKecil = 0; // Untuk Balita

        if ($activePeriod) {
            // JIKA ADA PERIODE AKTIF: Ambil data dari buku catatan harian besok
            $targetsBesok = DailyTarget::with('beneficiary')
                ->where('period_id', $activePeriod->id)
                ->where('date', $besok)
                ->where('is_holiday', false) // KUNCI: YANG LIBUR TIDAK DIHITUNG!
                ->get();

            foreach ($targetsBesok as $t) {
                if ($t->beneficiary->type === 'sekolah') {
                    $porsiSekolahBesar += $t->porsi_besar;
                    $porsiSekolahKecil += $t->porsi_kecil;
                } elseif ($t->beneficiary->type === 'posyandu') {
                    // Sesuai rumus Ahli Gizi: Bumil masuk Porsi Besar, Balita masuk Porsi Kecil
                    $porsiPosyanduBesar += $t->total_bumil_busui;
                    $porsiPosyanduKecil += $t->total_balita;
                }
            }
        } else {
            // JIKA TIDAK ADA PERIODE AKTIF: Gunakan Master Data sebagai perkiraan (Fallback)
            $porsiSekolahBesar = $beneficiaries->where('type', 'sekolah')->sum('porsi_besar');
            $porsiSekolahKecil = $beneficiaries->where('type', 'sekolah')->sum('porsi_kecil');
            $porsiPosyanduBesar = $beneficiaries->where('type', 'posyandu')->sum('total_bumil_busui');
            $porsiPosyanduKecil = $beneficiaries->where('type', 'posyandu')->sum('total_balita');
        }

        // MULAILAH MENGHITUNG KEBUTUHAN BERDASARKAN MENU YANG ADA BESOK
        // MULAILAH MENGHITUNG KEBUTUHAN BERDASARKAN MENU YANG ADA BESOK
        if ($jadwalBesokList->count() > 0) {
            foreach ($jadwalBesokList as $jadwal) {
                foreach ($jadwal->menu->items as $item) {
                    
                    if (!isset($kebutuhanItems[$item->id])) {
                        $kebutuhanItems[$item->id] = ['item' => $item, 'total_dibutuhkan' => 0];
                    }

                    $targetBesarUntukMenuIni = 0;
                    $targetKecilUntukMenuIni = 0;

                    // FILTER TARGET: Menu ini untuk siapa?
                    if ($jadwal->target_type === 'sekolah' || $jadwal->target_type === 'semua') {
                        $targetBesarUntukMenuIni += $porsiSekolahBesar;
                        $targetKecilUntukMenuIni += $porsiSekolahKecil;
                    }

                    if ($jadwal->target_type === 'posyandu' || $jadwal->target_type === 'semua') {
                        $targetBesarUntukMenuIni += $porsiPosyanduBesar;
                        $targetKecilUntukMenuIni += $porsiPosyanduKecil;
                    }

                    // 1. Hitung Kebutuhan dalam bentuk Gram / Mililiter (Sesuai resep)
                    $kebutuhanGram = ($item->pivot->gramasi_besar * $targetBesarUntukMenuIni) + 
                                     ($item->pivot->gramasi_kecil * $targetKecilUntukMenuIni);

                    // ================================================================
                    // 2. KONVERSI SATUAN (Dari Gram ke Kg / Dari Mililiter ke Liter)
                    // ================================================================
                    $kebutuhanFinal = $kebutuhanGram;
                    
                    // Jika satuan di gudang adalah 'kg' atau 'liter', maka wajib dibagi 1000
                    if (strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter') {
                        $kebutuhanFinal = $kebutuhanGram / 1000;
                    }
                    // ================================================================

                    // 3. Tambahkan ke total akumulasi item tersebut dengan angka yang sudah benar (Kg/Liter)
                    $kebutuhanItems[$item->id]['total_dibutuhkan'] += $kebutuhanFinal;
                }
            }

            // HITUNG DEFISIT DAN BIAYA
            foreach ($kebutuhanItems as $data) {
                $item = $data['item'];
                $totalDibutuhkan = $data['total_dibutuhkan'];

                if ($item->stock_system < $totalDibutuhkan) {
                    $defisit = $totalDibutuhkan - $item->stock_system;
                    $biayaEstimasi = $defisit * $item->hpp;
                    
                    $totalBiayaBesok += $biayaEstimasi;

                    $kebutuhanBesok[] = [
                        'name' => $item->name,
                        'defisit' => $defisit,
                        'unit' => $item->unit,
                        'biaya' => $biayaEstimasi
                    ];
                }
            }
        }

        // --- SISTEM PERINGATAN ALERGI MULTI-MENU ---
        $peringatanAlergen = [];
        if ($jadwalBesokList->count() > 0) {
            $itemIdsBesok = collect();
            foreach($jadwalBesokList as $jadwal) {
                $itemIdsBesok = $itemIdsBesok->merge($jadwal->menu->items->pluck('id'));
            }
            $itemIdsBesok = $itemIdsBesok->unique()->toArray();
            
            $penerimaAlergi = Beneficiary::with(['allergens' => function($q) use ($itemIdsBesok) {
                $q->whereIn('items.id', $itemIdsBesok);
            }])->whereHas('allergens', function($q) use ($itemIdsBesok) {
                $q->whereIn('items.id', $itemIdsBesok);
            })->get();

            foreach($penerimaAlergi as $penerima) {
                $bahanTerlarang = $penerima->allergens->pluck('name')->implode(', ');
                $peringatanAlergen[] = "{$penerima->school_name} alergi: {$bahanTerlarang}";
            }
        }
        // =========================================================================

        // =====================================================================
        // 5. FITUR VISUAL: DATA KALENDER TARGET HARIAN 14 HARI
        // =====================================================================
        $calendarData = [];

        if ($activePeriod) {
            $dailyTargets = DailyTarget::where('period_id', $activePeriod->id)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy('date');

            foreach ($dailyTargets as $date => $targets) {
                $totalSiswaHariIni = $targets->where('is_holiday', false)->sum(function ($t) {
                    return $t->porsi_besar + $t->porsi_kecil;
                });
                
                $totalPosyanduHariIni = $targets->where('is_holiday', false)->sum(function ($t) {
                    return $t->total_balita + $t->total_bumil_busui;
                });
                
                $totalLiburHariIni = $targets->where('is_holiday', true)->count();
                $carbonDate = Carbon::parse($date);
                
                $calendarData[] = [
                    'full_date' => $date,
                    'day_name'  => $carbonDate->translatedFormat('l'),
                    'day_num'   => $carbonDate->format('d'),
                    'month'     => $carbonDate->translatedFormat('M'),
                    'is_sunday' => $carbonDate->isSunday(),
                    'is_today'  => $date === now()->toDateString(),
                    'siswa'     => $totalSiswaHariIni,
                    'posyandu'  => $totalPosyanduHariIni,
                    'libur'     => $totalLiburHariIni,
                ];
            }
        }

        // 6. LEMPAR SEMUA DATA KE VIEW
        return view('dashboard', compact(
            'items', 
            'beneficiaries', 
            'totalSchools', 
            'totalPosyandu', 
            'totalStudents', 
            'totalJiwaPosyandu', 
            'lowStockCount', 
            'jadwalHariIni',
            'besok',
            'jadwalBesokList',
            'kebutuhanBesok',
            'totalBiayaBesok',
            'peringatanAlergen',
            'activePeriod',
            'calendarData'
        ));
    }
}