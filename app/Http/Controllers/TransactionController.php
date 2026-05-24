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

    // =========================================================================
    // JALUR VVIP: VERIFIKASI BARANG DATANG DARI AHLI GIZI
    // =========================================================================
    
    // Menampilkan halaman verifikasi barang datang dengan UI Kalender 14 Hari
    public function checkIncomingOrder(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        $calendarData = [];
        $pendingOrders = collect();

        // Pilihan tanggal target masak (Default: Hari ini atau tanggal pertama periode)
        $selectedDate = $request->date ?? now()->toDateString();

        if ($activePeriod) {
            $startDate = \Carbon\Carbon::parse($activePeriod->start_date);
            $endDate = \Carbon\Carbon::parse($activePeriod->end_date);

            // Jaga agar tanggal yang dipilih tidak melompat keluar dari periode aktif
            if ($selectedDate < $activePeriod->start_date || $selectedDate > $activePeriod->end_date) {
                $selectedDate = $activePeriod->start_date;
            }

            // 1. HITUNG HIT-COUNT PESANAN PENDING UNTUK INDIKATOR KALENDER
            $allOrdersInPeriod = DB::table('purchase_orders')
                ->whereBetween('date_of_cooking', [$activePeriod->start_date, $activePeriod->end_date])
                ->get()
                ->groupBy('date_of_cooking');

            // 2. GENERATE DERETAN 14 KOTAK KALENDER
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dString = $date->toDateString();
                $ordersOnDay = $allMenusInPeriod = $allOrdersInPeriod->get($dString, collect());

                $calendarData[] = [
                    'date'         => $dString,
                    'day_name'     => $date->translatedFormat('l'),
                    'day_num'      => $date->format('d'),
                    'month'        => $date->translatedFormat('M'),
                    'is_sunday'    => $date->isSunday(),
                    'is_selected'  => $dString === $selectedDate,
                    // Hitung berapa item pesanan yang belum diverifikasi (pending)
                    'pending_count'=> $ordersOnDay->where('status', 'pending')->count(),
                    'total_count'  => $ordersOnDay->count(),
                ];
            }

            // 3. TARIK DAFTAR PESANAN PENDING PADA TANGGAL YANG DIKLIK
            $pendingOrders = DB::table('purchase_orders')
                ->join('items', 'purchase_orders.item_id', '=', 'items.id')
                ->where('purchase_orders.date_of_cooking', $selectedDate)
                ->where('purchase_orders.status', 'pending')
                ->select('purchase_orders.*', 'items.name as item_name', 'items.unit as item_unit')
                ->get();
        }

        return view('transactions.incoming-check', compact('activePeriod', 'calendarData', 'selectedDate', 'pendingOrders'));
    }

    public function storeIncomingCheck(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'date_of_cooking' => 'required|date'
        ]);

        $activePeriod = Period::where('is_active', true)->first();

        DB::transaction(function() use ($request, $activePeriod) {
            foreach ($request->items as $orderId => $data) {
                $qtyReceived = $data['qty_received'] ?? 0;

                // 1. Tandai Surat Pesanan selesai
                DB::table('purchase_orders')->where('id', $orderId)->update([
                    'qty_received' => $qtyReceived,
                    'status'       => 'completed',
                    'updated_at'   => now()
                ]);

                $order = DB::table('purchase_orders')->where('id', $orderId)->first();
                $item = Item::find($order->item_id);

                if ($item && $qtyReceived > 0) {
                    // 2. Tambahkan fisik ke Master Stok Gudang
                    $item->increment('stock_system', $qtyReceived);

                    // 3. Catat di Riwayat Transaksi agar terbaca di Rekap Barang Masuk!
                    Transaction::create([
                        'period_id'   => $activePeriod ? $activePeriod->id : null,
                        'item_id'     => $order->item_id,
                        'type'        => 'in',
                        'quantity'    => $qtyReceived,
                        'date'        => now()->toDateString(), // Tanggal barang fisik masuk
                        'description' => 'Verifikasi PO masakan tanggal: ' . \Carbon\Carbon::parse($order->date_of_cooking)->translatedFormat('d M Y')
                    ]);
                }
            }
        });

        return redirect()->route('transactions.recap')->with('success', 'Barang datang berhasil diverifikasi! Stok gudang bertambah otomatis sesuai timbangan.');
    }

    // =========================================================================
    // JALUR DARURAT: INPUT BARANG KELUAR (PEMAKAIAN EKSTRA)
    // =========================================================================

    public function createOut()
    {
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        $periods = \App\Models\Period::orderBy('name', 'asc')->get();
        return view('transactions.out-create', compact('items', 'periods'));
    }

    public function storeOut(Request $request)
    {
        $request->validate([
            'period_id'   => 'required|exists:periods,id',
            'item_id'     => 'required|exists:items,id',
            'quantity'    => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'required|string', // Wajib diisi alasannya
        ]);

        $item = \App\Models\Item::find($request->item_id);

        if ($item->stock_system < $request->quantity) {
            return redirect()->back()->with('error', 'Gagal! Stok di sistem tidak mencukupi untuk dipotong.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $item) {
            // Catat sebagai barang keluar ('out')
            \App\Models\Transaction::create([
                'period_id'   => $request->period_id,
                'item_id'     => $request->item_id,
                'type'        => 'out', 
                'quantity'    => $request->quantity,
                'date'        => $request->date,
                'description' => '[EKSTRA/DARURAT] ' . $request->description,
            ]);

            // Potong stok gudang
            $item->decrement('stock_system', $request->quantity);
        });

        return redirect()->back()->with('success', 'Pemakaian darurat berhasil dicatat dan stok gudang telah dipotong!');
    }

    // =========================================================================
    // JALUR RETUR: PENGEMBALIAN SISA BAHAN DARI DAPUR KE GUDANG
    // =========================================================================

    public function createReturn()
    {
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        $periods = \App\Models\Period::orderBy('name', 'asc')->get();
        return view('transactions.return-create', compact('items', 'periods'));
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'period_id'   => 'required|exists:periods,id',
            'item_id'     => 'required|exists:items,id',
            'quantity'    => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'required|string',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            // Catat sebagai barang MASUK ('in') karena ini menambah stok
            \App\Models\Transaction::create([
                'period_id'   => $request->period_id,
                'item_id'     => $request->item_id,
                'type'        => 'in', 
                'quantity'    => $request->quantity,
                'date'        => $request->date,
                'description' => '[RETUR SISA] ' . $request->description,
            ]);

            // Tambahkan kembali ke stok gudang
            $item = \App\Models\Item::find($request->item_id);
            $item->increment('stock_system', $request->quantity);
        });

        return redirect()->back()->with('success', 'Sisa bahan berhasil dikembalikan dan stok gudang otomatis bertambah!');
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