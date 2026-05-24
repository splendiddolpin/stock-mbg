<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyMenu;
use App\Models\DailyTarget;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchasePlanController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        $rekapBahan = [];
        $calendarData = [];
        $menusHariIni = collect();
        
        // Ambil pilihan tanggal (Default: hari ini atau tanggal pertama periode)
        $selectedDate = $request->date ?? now()->toDateString();
        
        // Ambil semua master bahan untuk dropdown "Tambah Manual"
        $allItems = Item::orderBy('name', 'asc')->get();

        if ($activePeriod) {
            $startDate = Carbon::parse($activePeriod->start_date);
            $endDate = Carbon::parse($activePeriod->end_date);

            // Jika tanggal terpilih di luar jangkauan periode aktif, set ke tanggal pertama
            if ($selectedDate < $activePeriod->start_date || $selectedDate > $activePeriod->end_date) {
                $selectedDate = $activePeriod->start_date;
            }

            // 1. GENERATE KALENDER 14 HARI UNTUK PILIHAN MENU BELANJA
            $allMenusInPeriod = DailyMenu::whereBetween('date', [$activePeriod->start_date, $activePeriod->end_date])
                ->get()
                ->groupBy('date');

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dString = $date->toDateString();
                $menusOnDay = $allMenusInPeriod->get($dString, collect());

                $calendarData[] = [
                    'date'        => $dString,
                    'day_name'    => $date->translatedFormat('l'),
                    'day_num'     => $date->format('d'),
                    'month'       => $date->translatedFormat('M'),
                    'is_sunday'   => $date->isSunday(),
                    'is_selected' => $dString === $selectedDate,
                    'menu_count'  => $menusOnDay->count(),
                ];
            }

            // 2. TARIK SEMUA MENU PADA TANGGAL TERPILIH
            $menusHariIni = DailyMenu::with('menu.items')
                ->where('date', $selectedDate)
                ->get();

            if ($menusHariIni->isNotEmpty()) {
                // Tarik data target porsi harian pada tanggal tersebut (yang tidak libur)
                $targets = DailyTarget::with('beneficiary')
                    ->where('period_id', $activePeriod->id)
                    ->where('date', $selectedDate)
                    ->where('is_holiday', false)
                    ->get();

                // 3. HITUNG AKUMULASI BAHAN DARI SEMUA MENU DI HARI TERSEBUT
                foreach ($menusHariIni as $jadwal) {
                    $porsiBesarTarget = 0; 
                    $porsiKecilTarget = 0;

                    // Hitung porsi pengali berdasarkan target_type menu ini
                    foreach ($targets as $t) {
                        if ($jadwal->target_type === 'sekolah' || $jadwal->target_type === 'semua') {
                            if ($t->beneficiary->type === 'sekolah') {
                                $porsiBesarTarget += $t->porsi_besar;
                                $porsiKecilTarget += $t->porsi_kecil;
                            }
                        }
                        if ($jadwal->target_type === 'posyandu' || $jadwal->target_type === 'semua') {
                            if ($t->beneficiary->type === 'posyandu') {
                                $porsiBesarTarget += $t->total_bumil_busui;
                                $porsiKecilTarget += $t->total_balita;
                            }
                        }
                    }

                    // Gabungkan ke rekap bahan utama hari itu
                    foreach ($jadwal->menu->items as $item) {
                        if (!isset($rekapBahan[$item->id])) {
                            $rekapBahan[$item->id] = [
                                'id' => $item->id,
                                'name' => $item->name,
                                'unit' => $item->unit,
                                'total_kebutuhan' => 0
                            ];
                        }

                        $isKgLiter = strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter';
                        $kebGram = ($item->pivot->gramasi_besar * $porsiBesarTarget) + ($item->pivot->gramasi_kecil * $porsiKecilTarget);
                        $kebFinal = $isKgLiter ? ($kebGram / 1000) : $kebGram;
                        
                        // DI SINI PROSES PENJUMLAHANNYA (AKUMULASI)
                        $rekapBahan[$item->id]['total_kebutuhan'] += $kebFinal;
                    }
                }

                // Urutkan abjad bahan baku
                usort($rekapBahan, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
            }
        }

        return view('purchase-plan.index', compact('activePeriod', 'calendarData', 'selectedDate', 'menusHariIni', 'rekapBahan', 'allItems'));
    }
    // Menyimpan rencana belanja Ahli Gizi menjadi Surat Pesanan (PO)
    public function saveOrder(Request $request)
    {
        $request->validate([
            'date_of_cooking' => 'required|date',
            'orders' => 'required|array',
        ]);

        foreach ($request->orders as $order) {
            // Hanya simpan yang jumlah pesanannya lebih dari 0
            if (($order['qty_ordered'] ?? 0) > 0) {
                // Gunakan DB facade karena kita belum buat model PurchaseOrder
                \Illuminate\Support\Facades\DB::table('purchase_orders')->insert([
                    'date_of_cooking' => $request->date_of_cooking,
                    'item_id'         => $order['item_id'],
                    'qty_ordered'     => $order['qty_ordered'],
                    'status'          => 'pending',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        return redirect()->route('purchase-plan.index')->with('success', 'Surat Pesanan berhasil dikunci dan dikirim ke Admin Gudang!');
    }
}