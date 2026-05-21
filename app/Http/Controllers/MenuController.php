<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Item; // <-- Tambahan untuk memanggil Master Bahan
use Illuminate\Http\Request;
use App\Models\Beneficiary;

class MenuController extends Controller
{
    // 1. Menampilkan daftar menu
    public function index()
    {
        $menus = Menu::orderBy('name', 'asc')->get();
        return view('menus.index', compact('menus'));
    }

    // 2. Menampilkan form tambah menu
   // Menampilkan form tambah menu (Diperbarui)
    public function create()
    {
        // Panggil data items agar bisa dipilih di form tambah menu
        $items = Item::orderBy('name', 'asc')->get();
        return view('menus.create', compact('items'));
    }

    // Menyimpan menu dan resep baru (Diperbarui)
    public function store(\Illuminate\Http\Request $request)
    {
        // 1. Validasi data dasar menu terlebih dahulu
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 2. Simpan informasi utama menu ke tabel 'menus'
        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // 3. Tangkap dan rapihkan data resep (ingredients) dari form Alpine.js
        $itemIdsInMenu = [];
        if ($request->has('ingredients')) {
            $attachData = [];
            
            foreach ($request->ingredients as $ing) {
                // Pastikan admin sudah memilih bahan baku (item_id tidak boleh kosong)
                if (!empty($ing['item_id'])) {
                    $attachData[$ing['item_id']] = [
                        'gramasi_besar'  => $ing['gramasi_besar'] ?? 0,
                        'gramasi_kecil'  => $ing['gramasi_kecil'] ?? 0,
                        'gramasi_balita' => $ing['gramasi_balita'] ?? 0,
                        'gramasi_bumil'  => $ing['gramasi_bumil'] ?? 0,
                    ];
                    // Catat ID Bahan untuk pengecekan alergi nanti
                    $itemIdsInMenu[] = $ing['item_id'];
                }
            }

            // 4. Masukkan seluruh data gramasi (4 porsi) ke tabel penghubung (menu_ingredients)
            if (!empty($attachData)) {
                $menu->items()->attach($attachData);
            }
        }

        // =========================================================================
        // FITUR BARU: DETEKSI ALERGEN OTOMATIS SAAT TAMBAH MENU
        // =========================================================================
        // Cari tahu apakah ada penerima manfaat yang alergi dengan bahan resep baru ini
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
            
            // Mengembalikan dengan flash message custom warning agar bisa diwarnai oranye/merah di view
            return redirect()->route('menus.index')->with('warning', $pesanPeringatan);
        }
        // =========================================================================

        // Jika aman tanpa alergi, kembalikan pesan sukses biasa
        return redirect()->route('menus.index')->with('success', 'Menu dan Racikan Resep Baru berhasil disimpan!');
    }

    public function update(\Illuminate\Http\Request $request, Menu $menu)
    {
        // 1. Update data dasar menu
        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // 2. Siapkan data resep (ingredients) yang baru diedit
        $syncData = [];
        $itemIdsInMenu = [];
        if ($request->has('ingredients')) {
            foreach ($request->ingredients as $ing) {
                // Pastikan item_id tidak kosong
                if (!empty($ing['item_id'])) {
                    $syncData[$ing['item_id']] = [
                        'gramasi_besar' => $ing['gramasi_besar'] ?? 0,
                        'gramasi_kecil' => $ing['gramasi_kecil'] ?? 0,
                        'gramasi_balita' => $ing['gramasi_balita'] ?? 0,
                        'gramasi_bumil' => $ing['gramasi_bumil'] ?? 0,
                    ];
                    // Catat ID Bahan untuk pengecekan alergi nanti
                    $itemIdsInMenu[] = $ing['item_id'];
                }
            }
        }

        // 3. Gunakan perintah sakti "sync" untuk memperbarui tabel pivot secara otomatis!
        $menu->items()->sync($syncData);

        // =========================================================================
        // FITUR BARU: DETEKSI ALERGEN OTOMATIS SAAT UPDATE MENU
        // =========================================================================
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
        // =========================================================================

        return redirect()->route('menus.index')->with('success', 'Menu dan Resep berhasil diperbarui!');
    }
    // 4. Menampilkan detail menu (FUNGSI SHOW YANG BARU KITA BAHAS)
    public function show(Menu $menu)
    {
        // Memanggil semua data bahan untuk ditampilkan di form pilih bahan
        $items = Item::orderBy('name', 'asc')->get();
        
        return view('menus.show', compact('menu', 'items'));
    }

    // 5. Menampilkan form edit
    public function edit(Menu $menu)
    {
        // 1. Kita panggil semua data barang agar bisa dipilih di dropdown (sama seperti create)
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        
        return view('menus.edit', compact('menu', 'items'));
    }

    

    // 7. Menghapus menu
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus!');
    }
    // --- FUNGSI KHUSUS UNTUK RESEP (INGREDIENTS) ---

    // Fungsi menambah bahan dari halaman detail resep
    public function addIngredient(Request $request, Menu $menu)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'gramasi_besar' => 'required|numeric|min:0',
            'gramasi_kecil' => 'required|numeric|min:0',
        ]);

        // syncWithoutDetaching agar bahan yang sudah ada tidak hilang
        $menu->items()->syncWithoutDetaching([
            $request->item_id => [
                'gramasi_besar' => $request->gramasi_besar,
                'gramasi_kecil' => $request->gramasi_kecil,
            ]
        ]);

        return back()->with('success', 'Bahan berhasil ditambahkan ke resep!');
    }

    // Fungsi menghapus bahan dari resep
    public function removeIngredient(Menu $menu, Item $item)
    {
        // Melepas ikatan relasi antara menu dan bahan tersebut
        $menu->items()->detach($item->id);
        
        return back()->with('success', 'Bahan berhasil dihapus dari resep!');
    }
    // Fungsi untuk menghitung rencana kebutuhan bahan baku
    public function plan(Menu $menu)
{
    // 1. Hitung total seluruh porsi siswa DAN posyandu dari semua baris Beneficiary
    $totalPorsiBesar = Beneficiary::sum('porsi_besar') ?? 0;
    $totalPorsiKecil = Beneficiary::sum('porsi_kecil') ?? 0;
    $totalBalita     = Beneficiary::sum('total_balita') ?? 0;
    $totalBumilBusui = Beneficiary::sum('total_bumil_busui') ?? 0;

    // 2. Siapkan wadah (array) untuk menyimpan hasil perhitungan
    $kalkulasiKebutuhan = [];

    // 3. Lakukan perulangan untuk setiap bahan yang ada di resep menu ini
    foreach ($menu->items as $item) {
        
        // Hitung kebutuhan berdasarkan 4 kategori gramasi yang ada di resep pivot
        $kebutuhanBesar = ($item->pivot->gramasi_besar ?? 0) * $totalPorsiBesar;
        $kebutuhanKecil = ($item->pivot->gramasi_kecil ?? 0) * $totalPorsiKecil;
        $kebutuhanBalita = ($item->pivot->gramasi_balita ?? 0) * $totalBalita;
        $kebutuhanBumil  = ($item->pivot->gramasi_bumil ?? 0) * $totalBumilBusui;
        
        // Total kebutuhan dalam bentuk Gram / miliLiter (sesuai inputan resep)
        $totalKebutuhanGram = $kebutuhanBesar + $kebutuhanKecil + $kebutuhanBalita + $kebutuhanBumil;

        // OPSIONAL: Konversi ke satuan besar (Kg/Liter) jika satuan itemnya adalah Kg atau Liter
        // (Sesuaikan bagian ini jika di database kamu input gramasi resep menggunakan gram, tapi stok gudang menggunakan Kg)
        $totalKebutuhan = $totalKebutuhanGram;
        if (strtolower($item->unit) === 'kg' || strtolower($item->unit) === 'liter') {
            $totalKebutuhan = $totalKebutuhanGram / 1000;
        }

        // Kurangi dengan stok gudang saat ini
        $sisaStok = $item->stock_system - $totalKebutuhan;

        // Masukkan ke dalam array hasil beserta estimasi biaya belanja
        $defisit = $sisaStok < 0 ? abs($sisaStok) : 0;
        $estimasiBiaya = $defisit * ($item->hpp ?? 0); // Menghitung perkiraan biaya berdasarkan HPP item

        $kalkulasiKebutuhan[] = [
            'nama_bahan' => $item->name,
            'satuan' => $item->unit,
            'total_kebutuhan' => $totalKebutuhan,
            'stok_gudang' => $item->stock_system,
            'sisa_stok' => $sisaStok,
            'defisit' => $defisit,
            'biaya' => $estimasiBiaya // Dikirim agar bisa dipakai di view blade jika perlu
        ];
    }

    // 4. Kirim semua variabel ke view menus.plan
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