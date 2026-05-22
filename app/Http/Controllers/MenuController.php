<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Beneficiary;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('name', 'asc')->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $items = Item::orderBy('name', 'asc')->get();
        return view('menus.create', compact('items'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $itemIdsInMenu = [];
        if ($request->has('ingredients')) {
            $attachData = [];
            
            foreach ($request->ingredients as $ing) {
                if (!empty($ing['item_id'])) {
                    // KITA HANYA BUTUH 2 PORSI SEKARANG (BESAR & KECIL)
                    $attachData[$ing['item_id']] = [
                        'gramasi_besar'  => $ing['gramasi_besar'] ?? 0,
                        'gramasi_kecil'  => $ing['gramasi_kecil'] ?? 0,
                    ];
                    $itemIdsInMenu[] = $ing['item_id'];
                }
            }

            if (!empty($attachData)) {
                $menu->items()->attach($attachData);
            }
        }

        // DETEKSI ALERGEN
        $penerimaTerpengaruh = \App\Models\Beneficiary::whereHas('allergens', function($q) use ($itemIdsInMenu) {
            $q->whereIn('items.id', $itemIdsInMenu);
        })->with(['allergens' => function($q) use ($itemIdsInMenu) {
            $q->whereIn('items.id', $itemIdsInMenu);
        }])->get();

        if ($penerimaTerpengaruh->count() > 0) {
            $pesanPeringatan = 'Menu berhasil disimpan! ⚠️ PERHATIAN: ';
            $daftarPeringatan = [];
            
            foreach ($penerimaTerpengaruh as $penerima) {
                $namaBahanTerlarang = $penerima->allergens->pluck('name')->implode(', ');
                $daftarPeringatan[] = "{$penerima->school_name} (Alergi: {$namaBahanTerlarang})";
            }
            
            $pesanPeringatan .= implode(' | ', $daftarPeringatan);
            return redirect()->route('menus.index')->with('warning', $pesanPeringatan);
        }

        return redirect()->route('menus.index')->with('success', 'Menu dan Racikan Resep Baru berhasil disimpan!');
    }

    public function update(\Illuminate\Http\Request $request, Menu $menu)
    {
        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $syncData = [];
        $itemIdsInMenu = [];
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ing) {
                if (!empty($ing['item_id'])) {
                    // KITA HANYA BUTUH 2 PORSI SEKARANG (BESAR & KECIL)
                    $syncData[$ing['item_id']] = [
                        'gramasi_besar' => $ing['gramasi_besar'] ?? 0,
                        'gramasi_kecil' => $ing['gramasi_kecil'] ?? 0,
                    ];
                    $itemIdsInMenu[] = $ing['item_id'];
                }
            }
        }

        $menu->items()->sync($syncData);

        // DETEKSI ALERGEN
        $penerimaTerpengaruh = \App\Models\Beneficiary::whereHas('allergens', function($q) use ($itemIdsInMenu) {
            $q->whereIn('items.id', $itemIdsInMenu);
        })->with(['allergens' => function($q) use ($itemIdsInMenu) {
            $q->whereIn('items.id', $itemIdsInMenu);
        }])->get();

        if ($penerimaTerpengaruh->count() > 0) {
            $pesanPeringatan = 'Menu berhasil diperbarui! ⚠️ PERHATIAN RESEP: ';
            $daftarPeringatan = [];
            
            foreach ($penerimaTerpengaruh as $penerima) {
                $namaBahanTerlarang = $penerima->allergens->pluck('name')->implode(', ');
                $daftarPeringatan[] = "{$penerima->school_name} (Alergi: {$namaBahanTerlarang})";
            }
            
            $pesanPeringatan .= implode(' | ', $daftarPeringatan);
            return redirect()->route('menus.index')->with('warning', $pesanPeringatan);
        }

        return redirect()->route('menus.index')->with('success', 'Menu dan Resep berhasil diperbarui!');
    }

    public function show(Menu $menu)
    {
        $items = Item::orderBy('name', 'asc')->get();
        return view('menus.show', compact('menu', 'items'));
    }

    public function edit(Menu $menu)
    {
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        return view('menus.edit', compact('menu', 'items'));
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus!');
    }

    public function addIngredient(Request $request, Menu $menu)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'gramasi_besar' => 'required|numeric|min:0',
            'gramasi_kecil' => 'required|numeric|min:0',
        ]);

        $menu->items()->syncWithoutDetaching([
            $request->item_id => [
                'gramasi_besar' => $request->gramasi_besar,
                'gramasi_kecil' => $request->gramasi_kecil,
            ]
        ]);

        return back()->with('success', 'Bahan berhasil ditambahkan ke resep!');
    }

    public function removeIngredient(Menu $menu, Item $item)
    {
        $menu->items()->detach($item->id);
        return back()->with('success', 'Bahan berhasil dihapus dari resep!');
    }

    public function plan(Menu $menu)
    {
        $totalPorsiBesar = Beneficiary::sum('porsi_besar') ?? 0;
        $totalPorsiKecil = Beneficiary::sum('porsi_kecil') ?? 0;
        $totalBalita     = Beneficiary::sum('total_balita') ?? 0;
        $totalBumilBusui = Beneficiary::sum('total_bumil_busui') ?? 0;

        // Gabungkan targetnya untuk hitungan plan keseluruhan
        $gabunganPorsiBesar = $totalPorsiBesar + $totalBumilBusui;
        $gabunganPorsiKecil = $totalPorsiKecil + $totalBalita;

        $kalkulasiKebutuhan = [];

        foreach ($menu->items as $item) {
            
            // Perhitungan sekarang sangat simpel, cukup Besar & Kecil
            $kebutuhanBesar = ($item->pivot->gramasi_besar ?? 0) * $gabunganPorsiBesar;
            $kebutuhanKecil = ($item->pivot->gramasi_kecil ?? 0) * $gabunganPorsiKecil;
            
            $totalKebutuhanGram = $kebutuhanBesar + $kebutuhanKecil;

            $totalKebutuhan = $totalKebutuhanGram;
            if (strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter') {
                $totalKebutuhan = $totalKebutuhanGram / 1000;
            }

            $sisaStok = $item->stock_system - $totalKebutuhan;
            $defisit = $sisaStok < 0 ? abs($sisaStok) : 0;
            $estimasiBiaya = $defisit * ($item->hpp ?? 0);

            $kalkulasiKebutuhan[] = [
                'nama_bahan'      => $item->name,
                'satuan'          => $item->unit,
                'total_kebutuhan' => $totalKebutuhan,
                'stok_gudang'     => $item->stock_system,
                'sisa_stok'       => $sisaStok,
                'defisit'         => $defisit,
                'biaya'           => $estimasiBiaya
            ];
        }

        return view('menus.plan', compact(
            'menu', 
            'totalPorsiBesar', 
            'totalPorsiKecil', 
            'totalBalita', 
            'totalBumilBusui', 
            'kalkulasiKebutuhan'
        ));
    }
}