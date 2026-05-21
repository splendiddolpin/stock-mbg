<?php

namespace App\Http\Controllers;

use App\Models\DailyMenu;
use App\Models\Menu;
use Illuminate\Http\Request;

class DailyMenuController extends Controller
{
    // Menampilkan halaman jadwal sekaligus form tambah
    public function index()
    {
        $menus = Menu::orderBy('name', 'asc')->get();
        // Menampilkan jadwal dari hari ini ke depan
        $schedules = DailyMenu::with('menu')
                              ->orderBy('date', 'asc')
                              ->get();

        return view('daily_menus.index', compact('menus', 'schedules'));
    }

    // Menyimpan jadwal baru
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'menu_id' => 'required|exists:menus,id',
        ]);

        // Cek apakah tanggal tersebut sudah ada jadwalnya
        $cekJadwal = DailyMenu::where('date', $request->date)->first();
        if ($cekJadwal) {
            return back()->with('error', 'Tanggal tersebut sudah memiliki jadwal menu! Hapus jadwal lama terlebih dahulu.');
        }

        DailyMenu::create([
            'date' => $request->date,
            'menu_id' => $request->menu_id,
        ]);

        return back()->with('success', 'Jadwal menu berhasil ditambahkan!');
    }

    // Jangan lupa pastikan ada "use Illuminate\Support\Facades\DB;" di bagian atas file

    public function execute(\App\Models\DailyMenu $dailyMenu)
    {
        $menu = $dailyMenu->menu;
        $totalPorsiBesar = \App\Models\Beneficiary::sum('porsi_besar') ?? 0;
        $totalPorsiKecil = \App\Models\Beneficiary::sum('porsi_kecil') ?? 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($menu, $totalPorsiBesar, $totalPorsiKecil, $dailyMenu) {
            foreach ($menu->items as $item) {
                $totalKebutuhan = ($item->pivot->gramasi_besar * $totalPorsiBesar) + ($item->pivot->gramasi_kecil * $totalPorsiKecil);
                
                $jumlahPotong = $totalKebutuhan;
                if (strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter') {
                    $jumlahPotong = $totalKebutuhan / 1000;
                }

                // 1. Kurangi stok
                $item->decrement('stock_system', $jumlahPotong);

                // 2. Catat ke rekap penggunaan
                \App\Models\UsageRecap::create([
                    'date' => $dailyMenu->date,
                    'item_id' => $item->id,
                    'menu_id' => $menu->id,
                    'quantity_out' => $jumlahPotong,
                    'unit' => $item->unit,
                    'total_cost' => $jumlahPotong * $item->hpp,
                ]);
            }

            // 3. HAPUS JADWAL (Agar hilang dari kalender & dashboard)
            $dailyMenu->delete();
        });

        return redirect()->route('dashboard')->with('success', 'Menu hari ini berhasil diselesaikan! Stok dipotong, rekap dicatat, dan jadwal telah dibersihkan.');
    }

    // Menghapus jadwal
    public function destroy(DailyMenu $dailyMenu)
    {
        $dailyMenu->delete();
        return back()->with('success', 'Jadwal menu berhasil dihapus!');
    }
}