<?php

namespace App\Http\Controllers;

use App\Models\MenuCatalog;
use App\Models\MenuRequest; // <-- INI YANG TADI TERLEWAT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Dan pastikan ini juga ada untuk grafik

class MenuCatalogController extends Controller
{
    public function index()
    {
        // 1. Data untuk Tab Kelola Katalog (CRUD)
        $catalogs = MenuCatalog::orderBy('category')->orderBy('name')->get();

        // 2. Data untuk Tab Statistik Request Siswa
        $rankedRequests = MenuRequest::select('menu_name', DB::raw('count(*) as total_request'))
            ->groupBy('menu_name')
            ->orderBy('total_request', 'desc')
            ->get();
            
        $totalVotes = MenuRequest::count();
        $latestRequests = MenuRequest::with('beneficiary')->latest()->take(10)->get();

        // Kirim semua data ke satu view gabungan
        return view('menu-catalogs.index', compact('catalogs', 'rankedRequests', 'totalVotes', 'latestRequests'));
    }

    public function create()
    {
        $categories = ['Karbohidrat', 'Protein Hewani', 'Protein Nabati', 'Sayur', 'Buah', 'Susu'];
        return view('menu-catalogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
        ]);

        MenuCatalog::create($request->all());

        return redirect()->route('menu-catalogs.index')->with('success', 'Menu baru berhasil ditambahkan ke Katalog Publik!');
    }

    public function edit(MenuCatalog $menuCatalog)
    {
        $categories = ['Karbohidrat', 'Protein Hewani', 'Protein Nabati', 'Sayur', 'Buah', 'Susu'];
        return view('menu-catalogs.edit', compact('menuCatalog', 'categories'));
    }

    public function update(Request $request, MenuCatalog $menuCatalog)
    {
        $request->validate([
            'category' => 'required|string',
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
        ]);

        $menuCatalog->update($request->all());

        return redirect()->route('menu-catalogs.index')->with('success', 'Data menu katalog berhasil diperbarui!');
    }

    public function destroy(MenuCatalog $menuCatalog)
    {
        $menuCatalog->delete();
        return redirect()->route('menu-catalogs.index')->with('success', 'Menu berhasil dihapus dari Katalog Publik!');
    }
}