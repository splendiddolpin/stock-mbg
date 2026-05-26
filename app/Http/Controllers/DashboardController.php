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
use Illuminate\Support\Facades\DB;

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

        // Cari Periode Aktif
        $activePeriod = Period::where('is_active', true)->latest()->first();

        // 3. Ambil jadwal menu untuk HARI INI
        $hariIni = now()->toDateString();
        $jadwalHariIni = DailyMenu::with('menu.items')
                        ->where('date', $hariIni)
                        ->first();

        // =========================================================================
        // 4. LOGIKA KEBUTUHAN BELANJA BESOK (DIAMBIL DARI PURCHASE ORDER)
        // =========================================================================
        // =========================================================================
        // 4. LOGIKA KEBUTUHAN BELANJA BESOK (DIAMBIL DARI PURCHASE ORDER)
        // =========================================================================
        $besok = now()->addDay()->toDateString();
        $jadwalBesokList = DailyMenu::with('menu.items')->where('date', $besok)->get();
        
        $kebutuhanBesok = [];
        $totalBiayaBesok = 0;

        // Tarik data Surat Pesanan (Permintaan Dapur)
        $poBesok = \Illuminate\Support\Facades\DB::table('purchase_orders')
            ->join('items', 'purchase_orders.item_id', '=', 'items.id')
            ->where('purchase_orders.date_of_cooking', $besok)
            ->select('items.name', 'items.unit', 'items.hpp', 'items.stock_system', 'purchase_orders.qty_ordered', 'purchase_orders.status')
            ->get();

        foreach ($poBesok as $po) {
            $permintaanDapur = $po->qty_ordered;
            $stokGudang = $po->stock_system;
            
            // RUMUS BENAR: Kekurangan (Defisit) = Permintaan Dapur - Stok Gudang
            $defisit = $permintaanDapur - $stokGudang;

            // HANYA MASUK DAFTAR BELANJA JIKA DEFISIT > 0 (Artinya stok kurang!)
            if ($defisit > 0) {
                $biaya = $defisit * $po->hpp;
                
                $kebutuhanBesok[] = [
                    'name'       => $po->name,
                    'unit'       => $po->unit,
                    'permintaan' => $permintaanDapur,
                    'stok'       => $stokGudang,
                    'defisit'    => $defisit, // Ini yang harus dibeli ke pasar
                    'biaya'      => $biaya,
                    'status'     => $po->status
                ];
                
                $totalBiayaBesok += $biaya;
            }
        }

        // =========================================================================
        // --- SISTEM PERINGATAN ALERGI MULTI-MENU ---
        // =========================================================================
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