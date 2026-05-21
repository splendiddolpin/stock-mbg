<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function indexIn()
    {
        $transactions = Transaction::with(['item', 'period'])
                                   ->where('type', 'in')
                                   ->orderBy('date', 'desc')
                                   ->get();
                                   
        return view('transactions.in-index', compact('transactions'));
    }

    public function createIn()
    {
        $items = Item::orderBy('name', 'asc')->get();
        $periods = Period::orderBy('name', 'asc')->get();
        return view('transactions.in-create', compact('items', 'periods'));
    }

    public function storeIn(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:periods,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            // HPP dihapus dari validasi
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            Transaction::create([
                'period_id' => $request->period_id,
                'item_id' => $request->item_id,
                'type' => 'in',
                'quantity' => $request->quantity,
                // HPP dihapus dari create
                'date' => $request->date,
                'description' => $request->description,
            ]);

            $item = Item::find($request->item_id);
            $item->increment('stock_system', $request->quantity);
        });

        return redirect()->route('transactions.in')->with('success', 'Barang masuk disimpan dan stok berhasil ditambahkan!');
    }

    public function editIn($id)
    {
        $transaction = Transaction::findOrFail($id);
        $items = Item::orderBy('name', 'asc')->get();
        $periods = Period::orderBy('name', 'asc')->get();
        
        return view('transactions.in-edit', compact('transaction', 'items', 'periods'));
    }

    public function updateIn(Request $request, $id)
    {
        $request->validate([
            'period_id' => 'required|exists:periods,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            // HPP dihapus dari validasi
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $id) {
            $transaction = Transaction::findOrFail($id);
            
            $oldItem = Item::find($transaction->item_id);
            $oldItem->decrement('stock_system', $transaction->quantity);

            $transaction->update([
                'period_id' => $request->period_id,
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
                // HPP dihapus dari update
                'date' => $request->date,
                'description' => $request->description,
            ]);

            $newItem = Item::find($request->item_id);
            $newItem->increment('stock_system', $request->quantity);
        });

        return redirect()->route('transactions.in')->with('success', 'Data diperbarui dan penyesuaian stok berhasil dilakukan!');
    }

    public function destroyIn($id)
    {
        DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);
            
            $item = Item::find($transaction->item_id);
            $item->decrement('stock_system', $transaction->quantity);

            $transaction->delete();
        });

        return redirect()->route('transactions.in')->with('success', 'Data barang masuk dihapus dan stok telah dikembalikan!');
    }

    // --- FUNGSI REKAP ADA DI SINI ---
    public function recap()
    {
        $periods = Period::with(['transactions' => function($query) {
            $query->where('type', 'in')->orderBy('date', 'desc')->with('item');
        }])->orderBy('id', 'desc')->get();
                                   
        return view('transactions.recap', compact('periods'));
    }
    // --------------------------------

}