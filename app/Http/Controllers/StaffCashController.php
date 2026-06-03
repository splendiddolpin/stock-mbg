<?php

namespace App\Http\Controllers;

use App\Models\StaffCash;
use Illuminate\Http\Request;

class StaffCashController extends Controller
{
    public function index()
    {
        // Ambil data dari yang terbaru
        $transactions = StaffCash::orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        
        // Hitung Saldo Otomatis
        $totalIn = $transactions->where('type', 'in')->sum('amount');
        $totalOut = $transactions->where('type', 'out')->sum('amount');
        $balance = $totalIn - $totalOut;

        return view('staff-cash.index', compact('transactions', 'totalIn', 'totalOut', 'balance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'type'        => 'required|in:in,out',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        StaffCash::create($request->all());

        return redirect()->route('staff-cash.index')->with('success', 'Transaksi kas berhasil dicatat!');
    }

    public function destroy(StaffCash $staffCash)
    {
        $staffCash->delete();
        return redirect()->route('staff-cash.index')->with('success', 'Transaksi berhasil dihapus dan saldo telah disesuaikan.');
    }
}