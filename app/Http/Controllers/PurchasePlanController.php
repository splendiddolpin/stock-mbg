<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyMenu;
use App\Models\DailyTarget;
use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchasePlanController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        $rekapBahan = [];
        $calendarData = [];
        $menusHariIni = collect();
        
        $selectedDate = $request->date ?? now()->toDateString();
        $allItems = Item::orderBy('name', 'asc')->get();

        $hasExistingOrder = false;
        $poStatus = null;

        if ($activePeriod) {
            $startDate = Carbon::parse($activePeriod->start_date);
            $endDate = Carbon::parse($activePeriod->end_date);

            if ($selectedDate < $activePeriod->start_date || $selectedDate > $activePeriod->end_date) {
                $selectedDate = $activePeriod->start_date;
            }

            // 1. GENERATE KALENDER & INDIKATOR PO
            $allMenusInPeriod = DailyMenu::whereBetween('date', [$activePeriod->start_date, $activePeriod->end_date])
                ->get()->groupBy('date');
                
            $allOrdersInPeriod = DB::table('purchase_orders')
                ->whereBetween('date_of_cooking', [$activePeriod->start_date, $activePeriod->end_date])
                ->get()->groupBy('date_of_cooking');

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dString = $date->toDateString();
                $menusOnDay = $allMenusInPeriod->get($dString, collect());
                $ordersOnDay = $allOrdersInPeriod->get($dString, collect());
                
                $calendarData[] = [
                    'date'        => $dString,
                    'day_name'    => $date->translatedFormat('l'),
                    'day_num'     => $date->format('d'),
                    'month'       => $date->translatedFormat('M'),
                    'is_sunday'   => $date->isSunday(),
                    'is_selected' => $dString === $selectedDate,
                    'menu_count'  => $menusOnDay->count(),
                    'po_status'   => $ordersOnDay->isNotEmpty() ? $ordersOnDay->first()->status : null,
                ];
            }

            // 2. TARIK SEMUA MENU PADA TANGGAL TERPILIH
            $menusHariIni = DailyMenu::with('menu.items')->where('date', $selectedDate)->get();

            if ($menusHariIni->isNotEmpty()) {
                $targets = DailyTarget::with('beneficiary')
                    ->where('period_id', $activePeriod->id)
                    ->where('date', $selectedDate)
                    ->where('is_holiday', false)->get();

                // 3. HITUNG AKUMULASI BAHAN DARI RESEP
                foreach ($menusHariIni as $jadwal) {
                    $porsiBesarTarget = 0; $porsiKecilTarget = 0;
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

                    // --- BAGIAN INI YANG DIUBAH: TAMBAHKAN 'stock' ---
                    foreach ($jadwal->menu->items as $item) {
                        if (!isset($rekapBahan[$item->id])) {
                            $rekapBahan[$item->id] = [
                                'id' => $item->id, 
                                'name' => $item->name, 
                                'unit' => $item->unit, 
                                'stock' => $item->stock_system, // <-- Data stok diambil
                                'total_kebutuhan' => 0, 
                                'pesan' => 0
                            ];
                        }
                        $isKgLiter = strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter';
                        $kebGram = ($item->pivot->gramasi_besar * $porsiBesarTarget) + ($item->pivot->gramasi_kecil * $porsiKecilTarget);
                        $rekapBahan[$item->id]['total_kebutuhan'] += $isKgLiter ? ($kebGram / 1000) : $kebGram;
                    }
                }

                // 4. CEK & GABUNGKAN JIKA ADA PO LAMA DI DATABASE (EDIT MODE) ATAU SET DEFAULT NOL
                $existingPOs = DB::table('purchase_orders')->where('date_of_cooking', $selectedDate)->get();
                $hasExistingOrder = $existingPOs->isNotEmpty();
                
                if ($hasExistingOrder) {
                    $poStatus = $existingPOs->first()->status;
                    $poMap = $existingPOs->keyBy('item_id');

                    // Timpa angka pesanan dengan data yang pernah disave Ahli Gizi
                    foreach ($rekapBahan as $key => $bahan) {
                        if ($poMap->has($key)) {
                            $rekapBahan[$key]['pesan'] = $poMap[$key]->qty_ordered;
                            $poMap->forget($key);
                        } else {
                            // Hitung defisit pintar jika belum di-PO
                            $defisit = $bahan['total_kebutuhan'] - $bahan['stock'];
                            $rekapBahan[$key]['pesan'] = $defisit > 0 ? $defisit : 0;
                        }
                    }

                    // Jika dulu Ahli gizi nambah bumbu manual, masukkan juga ke list
                    foreach ($poMap as $item_id => $po) {
                        $itemMaster = Item::find($item_id);
                        if($itemMaster) {
                            $rekapBahan[$item_id] = [
                                'id' => $item_id, 'name' => $itemMaster->name . ' (Manual)',
                                'unit' => $itemMaster->unit, 'stock' => $itemMaster->stock_system,
                                'total_kebutuhan' => 0, 'pesan' => $po->qty_ordered
                            ];
                        }
                    }
                } else {
                    // --- LOGIKA BARU: Jika belum PO, hitung defisit. Jika stok cukup, set pesanan = 0 ---
                    foreach ($rekapBahan as $key => $bahan) {
                        $defisit = $bahan['total_kebutuhan'] - $bahan['stock'];
                        $rekapBahan[$key]['pesan'] = $defisit > 0 ? $defisit : 0;
                    }
                }

                usort($rekapBahan, function($a, $b) { return strcmp($a['name'], $b['name']); });
            }
        }

        return view('purchase-plan.index', compact('activePeriod', 'calendarData', 'selectedDate', 'menusHariIni', 'rekapBahan', 'allItems', 'hasExistingOrder', 'poStatus'));
    }

    public function saveOrder(Request $request)
    {
        $request->validate(['date_of_cooking' => 'required|date', 'orders' => 'required|array']);
        
        DB::transaction(function() use ($request) {
            foreach ($request->orders as $order) {
                if (($order['qty_ordered'] ?? 0) > 0) {
                    DB::table('purchase_orders')->insert([
                        'date_of_cooking' => $request->date_of_cooking,
                        'item_id' => $order['item_id'],
                        'qty_ordered' => $order['qty_ordered'],
                        'status' => 'pending',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });
        return redirect()->route('purchase-plan.index', ['date' => $request->date_of_cooking])->with('success', 'PO Berhasil Disimpan!');
    }

    // FUNGSI BARU: UPDATE PESANAN
    public function updateOrder(Request $request)
    {
        $request->validate(['date_of_cooking' => 'required|date', 'orders' => 'required|array']);
        
        // Cek status, kalau keburu diverifikasi Gudang, tolak!
        $status = DB::table('purchase_orders')->where('date_of_cooking', $request->date_of_cooking)->value('status');
        if($status === 'completed') {
            return redirect()->back()->with('error', 'Gagal update! Pesanan sudah diverifikasi Gudang.');
        }

        DB::transaction(function() use ($request) {
            // Hapus semua PO lama di tanggal itu
            DB::table('purchase_orders')->where('date_of_cooking', $request->date_of_cooking)->delete();
            
            // Masukkan form yang baru
            foreach ($request->orders as $order) {
                if (($order['qty_ordered'] ?? 0) > 0) {
                    DB::table('purchase_orders')->insert([
                        'date_of_cooking' => $request->date_of_cooking,
                        'item_id' => $order['item_id'],
                        'qty_ordered' => $order['qty_ordered'],
                        'status' => 'pending',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });
        return redirect()->route('purchase-plan.index', ['date' => $request->date_of_cooking])->with('success', 'Pesanan berhasil Diperbarui!');
    }

    // FUNGSI BARU: HAPUS PESANAN
    public function destroyOrder(Request $request)
    {
        $status = DB::table('purchase_orders')->where('date_of_cooking', $request->date_of_cooking)->value('status');
        if($status === 'completed') {
            return redirect()->back()->with('error', 'Gagal hapus! Pesanan sudah masuk stok Gudang.');
        }

        DB::table('purchase_orders')->where('date_of_cooking', $request->date_of_cooking)->delete();
        
        return redirect()->route('purchase-plan.index', ['date' => $request->date_of_cooking])->with('success', 'Seluruh daftar pesanan di tanggal tersebut berhasil dihapus!');
    }
}