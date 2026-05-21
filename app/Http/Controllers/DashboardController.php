<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Beneficiary;
use App\Models\Menu;
use App\Models\DailyMenu;

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

        // 3. Ambil jadwal menu untuk HARI INI
        $hariIni = now()->toDateString();
        $jadwalHariIni = DailyMenu::with('menu.items')
                        ->where('date', $hariIni)
                        ->first();

        // =========================================================================
        // 4. LOGIKA KEBUTUHAN BELANJA BESOK (UNTUK DITAMPILKAN DI BAWAH DASHBOARD)
        // =========================================================================
        $besok = now()->addDay()->toDateString();
        $jadwalBesok = DailyMenu::with('menu.items')->where('date', $besok)->first();
        
        // --- JURUS DETEKTIF TERAKHIR ---

        
        $kebutuhanBesok = [];
        $totalBiayaBesok = 0;

        // Ambil total target dari masing-masing porsi
        $totalPorsiBesar = $beneficiaries->sum('porsi_besar') ?? 0;
        $totalPorsiKecil = $beneficiaries->sum('porsi_kecil') ?? 0;
        $totalBalita     = $beneficiaries->sum('total_balita') ?? 0;
        $totalBumilBusui = $beneficiaries->sum('total_bumil_busui') ?? 0;

        if ($jadwalBesok) {
            foreach ($jadwalBesok->menu->items as $item) {
                
                // RUMUS ASLI TANPA DIBAGI 1000:
                // Kalikan langsung gramasi dengan masing-masing target
                $jumlahDibutuhkan = ($item->pivot->gramasi_besar * $totalPorsiBesar) + 
                                    ($item->pivot->gramasi_kecil * $totalPorsiKecil) +
                                    ($item->pivot->gramasi_balita * $totalBalita) + 
                                    ($item->pivot->gramasi_bumil * $totalBumilBusui);
                                    

                // Langsung cari kekurangan (defisit) stok gudang
                if ($item->stock_system < $jumlahDibutuhkan) {
                    $defisit = $jumlahDibutuhkan - $item->stock_system;
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

        $peringatanAlergen = [];
        if ($jadwalBesok) {
            $itemIdsBesok = $jadwalBesok->menu->items->pluck('id');
            
            // Cari penerima yang alergi terhadap bahan di menu besok
            $penerimaAlergi = \App\Models\Beneficiary::with(['allergens' => function($q) use ($itemIdsBesok) {
                $q->whereIn('items.id', $itemIdsBesok);
            }])->whereHas('allergens', function($q) use ($itemIdsBesok) {
                $q->whereIn('items.id', $itemIdsBesok);
            })->get();

            // Rangkai kalimat peringatannya
            foreach($penerimaAlergi as $penerima) {
                $bahanTerlarang = $penerima->allergens->pluck('name')->implode(', ');
                $peringatanAlergen[] = "{$penerima->school_name} alergi terhadap: {$bahanTerlarang}";
            }
        }

        // 5. Lempar semua data ke view dashboard.blade.php
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
            'jadwalBesok',
            'kebutuhanBesok',
            'totalBiayaBesok',
            'peringatanAlergen'
        ));
    }
}