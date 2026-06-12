<?php

namespace App\Http\Controllers;

use App\Models\UsageRecap;
use App\Models\Transaction; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator; // Tambahkan ini
use Illuminate\Pagination\LengthAwarePaginator; // Tambahkan ini

class UsageRecapController extends Controller
{
    public function index()
    {
        // 1. Ambil data pengeluaran OTOMATIS (dari dapur/resep)
        $autoRecaps = UsageRecap::with(['item', 'menu'])->get()->map(function($r) {
            return (object) [
                'source' => 'auto',
                'date' => $r->date,
                'item_name' => $r->item->name ?? 'Bahan Dihapus',
                'target_name' => $r->menu->name ?? '-',
                'quantity' => $r->quantity_out,
                'unit' => $r->unit,
                'total_cost' => $r->total_cost,
                'created_at' => $r->created_at
            ];
        });

        // 2. Ambil data pengeluaran MANUAL (darurat) dari tabel transaksi
        $manualRecaps = Transaction::with('item')->where('type', 'out')->get()->map(function($t) {
            return (object) [
                'source' => 'manual',
                'date' => $t->date,
                'item_name' => $t->item->name ?? 'Bahan Dihapus',
                'target_name' => $t->description, // Deskripsi jadi nama tujuan
                'quantity' => $t->quantity,
                'unit' => $t->item->unit ?? '-',
                'total_cost' => $t->quantity * ($t->item->hpp ?? 0),
                'created_at' => $t->created_at
            ];
        });

        // 3. Gabungkan kedua data, lalu urutkan (Terbaru di atas)
        $allData = $autoRecaps->concat($manualRecaps)
                             ->sortByDesc('created_at')
                             ->sortByDesc('date')
                             ->values();

        // 4. Buat Paginasi Manual untuk Data Gabungan
        $perPage = 15;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $items = $allData->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $recaps = new LengthAwarePaginator($items, $allData->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
        ]);

        return view('usage_recaps.index', compact('recaps'));
    }
}