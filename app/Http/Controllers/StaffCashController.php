<?php

namespace App\Http\Controllers;

use App\Models\StaffCash;
use Illuminate\Http\Request;

class StaffCashController extends Controller
{
    public function index()
    {
        $cashes = StaffCash::orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        $saldo = StaffCash::where('type', 'in')->sum('amount') - StaffCash::where('type', 'out')->sum('amount');
        
        // Hitung total uang yang masih nyangkut (utang belum lunas)
        $totalUtang = StaffCash::where('is_debt', true)->where('is_paid', false)->sum('amount');

        return view('staff-cash.index', compact('cashes', 'saldo', 'totalUtang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category' => 'required|string|in:kas,mitra,operasional,bahan_makanan'
        ]);

        // Logika Pintar: Jika ini pengeluaran (OUT) dan BUKAN untuk kas murni, maka otomatis jadi UTANG
        $is_debt = false;
        if ($request->type === 'out' && $request->category !== 'kas') {
            $is_debt = true;
        }

        StaffCash::create([
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'category' => $request->category,
            'is_debt' => $is_debt,
            'is_paid' => false, // Default belum dibayar
        ]);

        return redirect()->route('staff-cash.index')->with('success', 'Transaksi kas berhasil dicatat!');
    }

    // FUNGSI BARU: PELUNASAN UTANG
    public function payDebt($id)
    {
        $cash = StaffCash::findOrFail($id);

        if ($cash->is_debt && !$cash->is_paid) {
            // 1. Tandai lunas
            $cash->update(['is_paid' => true]);

            // 2. Otomatis catat pemasukan kas (Uang kembali ke kas)
            StaffCash::create([
                'date' => now()->toDateString(),
                'type' => 'in',
                'amount' => $cash->amount,
                'description' => 'Pengembalian Dana (Pelunasan: ' . $cash->description . ')',
                'category' => 'kas',
                'is_debt' => false,
                'is_paid' => false,
            ]);

            return redirect()->route('staff-cash.index')->with('success', 'Utang berhasil dilunasi! Uang telah kembali ke Saldo Kas.');
        }

        return redirect()->back()->with('error', 'Transaksi tidak valid untuk pelunasan.');
    }

    public function destroy($id)
    {
        StaffCash::findOrFail($id)->delete();
        return redirect()->route('staff-cash.index')->with('success', 'Data kas berhasil dihapus.');
    }
}