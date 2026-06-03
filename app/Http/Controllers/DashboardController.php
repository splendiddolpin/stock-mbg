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
        // 4. LOGIKA KEBUTUHAN BELANJA BESOK (DINAMIS: GRAMASI x PORSI AKTIF)
        // =========================================================================
        $besok = now()->addDay()->toDateString();
        $jadwalBesokList = DailyMenu::with('menu.items')->where('date', $besok)->get();
        
        $kebutuhanBesok = [];
        $totalBiayaBesok = 0;

        // A. Cek daftar sekolah/posyandu yang LIBUR besok
        $liburBesokIds = DailyTarget::where('date', $besok)
                            ->where('is_holiday', true)
                            ->pluck('beneficiary_id')
                            ->toArray();

        // B. Hitung PM Aktif Besok (Data yang Libur TIDAK DIHITUNG)
        // Asumsi: Sekolah pakai porsi_besar & porsi_kecil
        $porsiBesarSekolah = $beneficiaries->where('type', 'sekolah')->whereNotIn('id', $liburBesokIds)->sum('porsi_besar');
        $porsiKecilSekolah = $beneficiaries->where('type', 'sekolah')->whereNotIn('id', $liburBesokIds)->sum('porsi_kecil');
        
        // Asumsi: Posyandu pakai bumil (besar) & balita (kecil)
        $porsiBesarPosyandu = $beneficiaries->where('type', 'posyandu')->whereNotIn('id', $liburBesokIds)->sum('total_bumil_busui');
        $porsiKecilPosyandu = $beneficiaries->where('type', 'posyandu')->whereNotIn('id', $liburBesokIds)->sum('total_balita');

        // Penampung sementara agar bahan yang sama di menu berbeda bisa dijumlahkan
        $tempKebutuhan = [];

        // C. Kalkulasi Gramasi per Bahan
        foreach ($jadwalBesokList as $jadwal) {
            $targetBesar = 0;
            $targetKecil = 0;

            // Masukkan jumlah porsi sesuai target menu
            if ($jadwal->target_type == 'sekolah' || $jadwal->target_type == 'semua') {
                $targetBesar += $porsiBesarSekolah;
                $targetKecil += $porsiKecilSekolah;
            }
            if ($jadwal->target_type == 'posyandu' || $jadwal->target_type == 'semua') {
                $targetBesar += $porsiBesarPosyandu;
                $targetKecil += $porsiKecilPosyandu;
            }

            // Hitung bahan-bahan di dalam resepnya
            foreach ($jadwal->menu->items as $item) {
                $gBesar = (float) ($item->pivot->gramasi_besar ?? 0);
                $gKecil = (float) ($item->pivot->gramasi_kecil ?? 0);
                
                // RUMUS AKURAT: (Gramasi Besar * Target Besar Aktif) + (Gramasi Kecil * Target Kecil Aktif)
                $totalKebutuhanGram = ($gBesar * $targetBesar) + ($gKecil * $targetKecil);
                
                // Konversi Satuan ke sistem Gudang (KG / Liter)
                $satuan = strtolower($item->unit);
                $jmlKonversi = $totalKebutuhanGram;
                if (in_array($satuan, ['kg', 'liter'])) {
                    $jmlKonversi = $totalKebutuhanGram / 1000;
                }

                if (!isset($tempKebutuhan[$item->id])) {
                    $tempKebutuhan[$item->id] = [
                        'name'       => $item->name,
                        'unit'       => $item->unit,
                        'hpp'        => $item->hpp,
                        'stok'       => floatval($item->stock_system),
                        'permintaan' => 0,
                    ];
                }
                $tempKebutuhan[$item->id]['permintaan'] += $jmlKonversi;
            }
        }

        // D. Hitung Defisit (Kekurangan Stok) & Perkiraan Biaya Belanja
        foreach ($tempKebutuhan as $id => $data) {
            $defisit = $data['permintaan'] - $data['stok'];

            // Hanya masuk keranjang belanja jika stok kurang (defisit > 0)
            if ($defisit > 0) {
                $biaya = $defisit * $data['hpp'];
                
                $kebutuhanBesok[] = [
                    'name'       => $data['name'],
                    'unit'       => $data['unit'],
                    'permintaan' => $data['permintaan'],
                    'stok'       => $data['stok'],
                    'defisit'    => $defisit,
                    'biaya'      => $biaya,
                    'status'     => 'pending' // Status standar
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

        // Ambil Data Resep Terbaru untuk UI Kotak Bawah
        $menusWithItems = Menu::with('items')->latest()->take(5)->get();

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
            'calendarData',
            'menusWithItems' // Jangan lupa ini dikirim agar UI bawah tidak error
        ));
    }
}