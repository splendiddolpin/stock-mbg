<?php

namespace App\Http\Controllers;

use App\Models\DailyMenu;
use App\Models\Menu;
use App\Models\DailyTarget;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyMenuController extends Controller
{
    // Menampilkan halaman jadwal sekaligus form tambah
    public function index()
    {
        // 1. Ambil periode yang sedang aktif
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        
        $menus = \App\Models\Menu::orderBy('name', 'asc')->get();
        
        // 2. Tampilkan jadwal HANYA yang ada di dalam periode aktif ini (agar rapi)
        $schedules = collect();
        if ($activePeriod) {
            $schedules = \App\Models\DailyMenu::with('menu')
                ->whereBetween('date', [$activePeriod->start_date, $activePeriod->end_date])
                ->orderBy('date', 'asc')
                ->get();
        }

        // Kirim $activePeriod ke view
        return view('daily-menus.index', compact('menus', 'schedules', 'activePeriod'));
    }

    // Menyimpan jadwal baru
    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'menu_id'     => 'required|exists:menus,id',
            'target_type' => 'required|in:sekolah,posyandu,semua',
        ]);

        DailyMenu::create([
            'date'        => $request->date,
            'menu_id'     => $request->menu_id,
            'target_type' => $request->target_type,
        ]);

        return back()->with('success', 'Jadwal menu berhasil ditambahkan!');
    }

    // Eksekusi (Potong Stok & Catat Penggunaan)
    public function execute(\App\Models\DailyMenu $dailyMenu)
    {
        $menu = $dailyMenu->menu;
        
        // WADAH TARGET MURNI HARI INI
        $porsiBesarTarget = 0;
        $porsiKecilTarget = 0;

        // 1. CARI TARGET HARIAN DI TANGGAL MENU INI DIEKSEKUSI
        $targetsHariIni = DailyTarget::with('beneficiary')
            ->where('date', $dailyMenu->date)
            ->where('is_holiday', false) // KUNCI: YANG LIBUR TIDAK AKAN MEMOTONG STOK!
            ->get();

        // 2. KELOMPOKKAN TARGET SESUAI TARGET MENU (Sekolah / Posyandu)
        if ($targetsHariIni->count() > 0) {
            foreach ($targetsHariIni as $t) {
                // Jika menu ini untuk sekolah atau semuanya
                if ($dailyMenu->target_type === 'sekolah' || $dailyMenu->target_type === 'semua') {
                    if ($t->beneficiary->type === 'sekolah') {
                        $porsiBesarTarget += $t->porsi_besar;
                        $porsiKecilTarget += $t->porsi_kecil;
                    }
                }

                // Jika menu ini untuk posyandu atau semuanya
                if ($dailyMenu->target_type === 'posyandu' || $dailyMenu->target_type === 'semua') {
                    if ($t->beneficiary->type === 'posyandu') {
                        // Sesuai rumus Ahli Gizi: Bumil = Besar, Balita = Kecil
                        $porsiBesarTarget += $t->total_bumil_busui;
                        $porsiKecilTarget += $t->total_balita;
                    }
                }
            }
        } else {
            // FALLBACK: Jika kalender harian belum di-generate, pakai master data
            $beneficiaries = Beneficiary::all();
            foreach ($beneficiaries as $b) {
                if ($dailyMenu->target_type === 'sekolah' || $dailyMenu->target_type === 'semua') {
                    if ($b->type === 'sekolah') {
                        $porsiBesarTarget += $b->porsi_besar;
                        $porsiKecilTarget += $b->porsi_kecil;
                    }
                }
                if ($dailyMenu->target_type === 'posyandu' || $dailyMenu->target_type === 'semua') {
                    if ($b->type === 'posyandu') {
                        $porsiBesarTarget += $b->total_bumil_busui;
                        $porsiKecilTarget += $b->total_balita;
                    }
                }
            }
        }

        // 3. MULAI TRANSAKSI PEMOTONGAN STOK
        DB::transaction(function () use ($menu, $porsiBesarTarget, $porsiKecilTarget, $dailyMenu) {
            foreach ($menu->items as $item) {
                
                // RUMUS SUPER BERSIH: Cuma 2 Gramasi!
                $totalKebutuhan = ($item->pivot->gramasi_besar * $porsiBesarTarget) + 
                                  ($item->pivot->gramasi_kecil * $porsiKecilTarget);
                
                $jumlahPotong = $totalKebutuhan;
                if (strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter') {
                    $jumlahPotong = $totalKebutuhan / 1000;
                }

                // A. Kurangi stok gudang
                $item->decrement('stock_system', $jumlahPotong);

                // B. Catat ke rekap penggunaan agar laporannya akurat
                \App\Models\UsageRecap::create([
                    'date'         => $dailyMenu->date,
                    'item_id'      => $item->id,
                    'menu_id'      => $menu->id,
                    'quantity_out' => $jumlahPotong,
                    'unit'         => $item->unit,
                    'total_cost'   => $jumlahPotong * $item->hpp,
                ]);
            }

            // C. HAPUS JADWAL (Agar hilang dari kalender & dashboard)
            $dailyMenu->delete();
        });

        return redirect()->route('dashboard')->with('success', 'Menu hari ini berhasil diselesaikan! Stok dipotong secara akurat sesuai kalender libur/target.');
    }

    // Menghapus jadwal
    public function destroy(DailyMenu $dailyMenu)
    {
        $dailyMenu->delete();
        return back()->with('success', 'Jadwal menu berhasil dihapus!');
    }
}