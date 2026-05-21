<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Siapkan 3 tanggal berbeda untuk melihat efek grouping per tanggal
        $hariIni = Carbon::now()->format('Y-m-d');
        $kemarin = Carbon::now()->subDay()->format('Y-m-d');
        $lusaLalu = Carbon::now()->subDays(2)->format('Y-m-d');

        // Kita buat 15 data dummy agar tembus lebih dari 1 periode (12 data)
        $dummyData = [
            // --- TANGGAL: LUSA LALU (5 Transaksi) ---
            ['item_id' => 1, 'quantity' => 100, 'date' => $lusaLalu, 'desc' => 'Bantuan Bulog Tahap 1'],
            ['item_id' => 2, 'quantity' => 50,  'date' => $lusaLalu, 'desc' => 'Supplier Pasar Pagi'],
            ['item_id' => 3, 'quantity' => 10,  'date' => $lusaLalu, 'desc' => 'Peternakan Ayam Lokal'],
            ['item_id' => 4, 'quantity' => 30,  'date' => $lusaLalu, 'desc' => 'Petani Sayur Mertoyudan'],
            ['item_id' => 1, 'quantity' => 50,  'date' => $lusaLalu, 'desc' => 'Tambahan Beras Donatur'],

            // --- TANGGAL: KEMARIN (5 Transaksi) ---
            ['item_id' => 2, 'quantity' => 20,  'date' => $kemarin, 'desc' => 'Supplier Pasar Pagi'],
            ['item_id' => 3, 'quantity' => 15,  'date' => $kemarin, 'desc' => 'Peternakan Ayam Lokal'],
            ['item_id' => 4, 'quantity' => 25,  'date' => $kemarin, 'desc' => 'Petani Sayur Magelang'],
            ['item_id' => 1, 'quantity' => 100, 'date' => $kemarin, 'desc' => 'Bantuan Bulog Tahap 2'],
            ['item_id' => 2, 'quantity' => 30,  'date' => $kemarin, 'desc' => 'Supplier Daging Segar'],

            // --- TANGGAL: HARI INI (5 Transaksi) ---
            ['item_id' => 3, 'quantity' => 25,  'date' => $hariIni, 'desc' => 'Peternakan Ayam Lokal (Restock)'],
            ['item_id' => 4, 'quantity' => 40,  'date' => $hariIni, 'desc' => 'Panen Sayur Segar'],
            ['item_id' => 1, 'quantity' => 150, 'date' => $hariIni, 'desc' => 'Bantuan Logistik Pusat'],
            ['item_id' => 2, 'quantity' => 40,  'date' => $hariIni, 'desc' => 'Supplier Pasar Pagi'],
            ['item_id' => 3, 'quantity' => 30,  'date' => $hariIni, 'desc' => 'Donasi Telur Warga'],
        ];

        // Gunakan DB Transaction agar aman
        DB::transaction(function () use ($dummyData) {
            foreach ($dummyData as $data) {
                // 1. Buat record transaksi masuk
                Transaction::create([
                    'item_id' => $data['item_id'],
                    'type' => 'masuk',
                    'quantity' => $data['quantity'],
                    'date' => $data['date'],
                    'description' => $data['desc'],
                ]);

                // 2. Tambahkan stok ke master barang
                $item = Item::find($data['item_id']);
                if ($item) {
                    $item->increment('stock_system', $data['quantity']);
                }
            }
        });
    }
}