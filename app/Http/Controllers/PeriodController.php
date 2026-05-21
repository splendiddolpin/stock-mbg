<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction; 
use App\Models\DailyMenu;
use App\Models\UsageRecap;

class PeriodController extends Controller
{
    public function create()
    {
        return view('periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        // Hitung otomatis 14 Hari (Start Date + 13 Hari)
        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays(13); 

        Period::create([
            'name' => $request->name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_active' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Periode baru (14 Hari) berhasil dibuat!');
    }

    public function resetPeriod(\Illuminate\Http\Request $request)
    {
        // 1. Matikan pengecekan gembok relasi sementara agar tidak error
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        // 2. Bersihkan Rekap Penggunaan (Bahan Keluar)
        \App\Models\UsageRecap::truncate();

        // 3. Bersihkan Rekap Barang Masuk
        \App\Models\Transaction::truncate();

        // 4. Bersihkan sisa jadwal menu harian
        \App\Models\DailyMenu::truncate();

        // 5. Bersihkan Kotak Periode
        \App\Models\Period::truncate();

        // 6. Nyalakan kembali pengecekan gembok relasi
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Langsung kembalikan ke halaman dashboard
        return redirect()->route('dashboard')->with('success', 'Reset Total Berhasil! Semua rekap, transaksi, dan riwayat periode lama telah dibersihkan hingga ke akar-akarnya.');
    }
    
    // Relasi dari Period ke Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}