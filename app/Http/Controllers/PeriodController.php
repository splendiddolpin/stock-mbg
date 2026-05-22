<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyTarget;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        // 1. Hitung otomatis 14 Hari (Start Date + 13 Hari)
        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays(13); 

        // 2. Buat & Simpan Periode Utama
        $period = Period::create([
            'name' => $request->name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_active' => true,
        ]);

        // 3. TARIK DATA MASTER PM
        $beneficiaries = Beneficiary::all();

        // Looping selama 14 Hari penuh
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            
            $isSunday = $date->isSunday(); // Cek apakah hari ini Minggu

            foreach ($beneficiaries as $pm) {
                $isHoliday = false;
                
                // Ambil data asli dari database PM
                $pBesar  = $pm->porsi_besar;
                $pKecil  = $pm->porsi_kecil;
                $tBalita = $pm->total_balita;
                $tBumil  = $pm->total_bumil_busui;

                if ($isSunday) {
                    // Jika MINGGU -> Semua libur total!
                    $pBesar = $pKecil = $tBalita = $tBumil = 0;
                    $isHoliday = true;
                } else {
                    // JIKA HARI KERJA (Senin - Sabtu)
                    
                    // =========================================================
                    // INI PERUBAHAN BARU: JIKA SABTU & TIPENYA SEKOLAH -> LIBUR
                    // =========================================================
                    if ($date->isSaturday() && $pm->type === 'sekolah') {
                        $pBesar = 0;
                        $pKecil = 0;
                        $isHoliday = true;
                    }
                    // =========================================================

                    // LOGIKA RAPELAN POSYANDU (Tetap Aman)
                    if ($pm->type === 'posyandu') {
                        if ($date->isMonday() || $date->isThursday()) {
                            // Senin & Kamis: Posyandu dikali 3!
                            $tBalita *= 3;
                            $tBumil  *= 3;
                        } else {
                            // Selasa, Rabu, Jumat, Sabtu: Posyandu 0 (Karena dirapel)
                            $tBalita = 0;
                            $tBumil  = 0;
                        }
                    }
                }

                // Simpan porsi yang sudah dihitung ke tabel Target Harian
                DailyTarget::create([
                    'period_id'         => $period->id,
                    'date'              => $date->toDateString(),
                    'beneficiary_id'    => $pm->id,
                    'porsi_besar'       => $pBesar,
                    'porsi_kecil'       => $pKecil,
                    'total_balita'      => $tBalita,
                    'total_bumil_busui' => $tBumil,
                    'is_holiday'        => $isHoliday,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Periode baru (14 Hari) berhasil dibuat! Sekolah otomatis libur di hari Sabtu & Minggu.');
    }

    public function resetPeriod(\Illuminate\Http\Request $request)
    {
        // 1. Matikan pengecekan gembok relasi sementara agar tidak error
        Schema::disableForeignKeyConstraints();
        
        // 2. Bersihkan Rekap Penggunaan (Bahan Keluar)
        UsageRecap::truncate();

        // 3. Bersihkan Rekap Barang Masuk
        Transaction::truncate();

        // 4. Bersihkan sisa jadwal menu harian
        DailyMenu::truncate();

        // 5. Bersihkan Target Harian yang baru kita buat
        DailyTarget::truncate();

        // 6. Bersihkan Kotak Periode
        Period::truncate();

        // 7. Nyalakan kembali pengecekan gembok relasi
        Schema::enableForeignKeyConstraints();

        // Langsung kembalikan ke halaman dashboard
        return redirect()->route('dashboard')->with('success', 'Reset Total Berhasil! Semua rekap, transaksi, target harian, dan riwayat periode telah dibersihkan hingga ke akar-akarnya.');
    }
}