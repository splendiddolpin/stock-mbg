<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 1. Menampilkan daftar semua bahan
    public function index()
    {
        $items = Item::orderBy('name', 'asc')->get();
        return view('items.index', compact('items'));
    }

    // 2. Menampilkan form tambah bahan (Sudah ada sebelumnya)
    public function create()
    {
        return view('items.create');
    }

    // 3. Menyimpan bahan baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:kg,liter,pcs,pack',
            'min_stock_warning' => 'required|integer|min:0',
            'hpp' => 'required|integer|min:0', 
        ]);

        Item::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'stock_system' => 0, 
            'min_stock_warning' => $request->min_stock_warning,
            'hpp' => $request->hpp, 
        ]);

        // Berubah: Setelah simpan, arahkan ke daftar item
        return redirect()->route('items.index')->with('success', 'Bahan baru beserta HPP berhasil didaftarkan!');
    }

    // 4. Menampilkan form edit bahan
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    // 5. Menyimpan perubahan data bahan
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|in:kg,liter,pcs,pack',
            'min_stock_warning' => 'required|integer|min:0',
            'hpp' => 'required|integer|min:0', 
        ]);

        $item->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'min_stock_warning' => $request->min_stock_warning,
            'hpp' => $request->hpp, 
        ]);

        return redirect()->route('items.index')->with('success', 'Data bahan berhasil diperbarui!');
    }

    // 6. Menghapus bahan
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Data bahan berhasil dihapus!');
    }
}