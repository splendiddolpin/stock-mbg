<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Period;
use App\Models\DailyTarget;
use App\Models\UsageRecap;
use App\Models\Transaction;
use App\Models\DailyMenu;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AutoClosePeriod extends Command
{
    // Nama perintah untuk dijalankan di terminal
    protected $signature = 'mbg:auto-close-period';
    protected $description = 'Otomatis tutup periode, cetak excel, dan sapu DB pada hari Minggu 00:01';

    public function handle()
    {
        // 1. CARI PERIODE YANG SEDANG AKTIF SAAT INI
        $period = Period::where('is_active', true)->first();

        // Jika tidak ada periode aktif, hentikan robot
        if (!$period) {
            $this->info('Tidak ada periode aktif untuk ditutup otomatis.');
            return;
        }

        // 2. SIAPKAN NAMA FILE EXCEL
        $startDateStr = Carbon::parse($period->start_date)->format('Y-m-d');
        $endDateStr = Carbon::parse($period->end_date)->format('Y-m-d');
        $periodName = Str::slug($period->name, '_');
        $fileName = "Arsip_Matang_MBG_{$periodName}_FROZEN.xls";
        $savePath = "excel_archives/{$fileName}";

        // 3. CETAK EXCEL SEKARANG JUGA (SAAT DATA MASIH UTUH DI DB!!!)
        $excelContent = view('periods.export', compact('period'))->render();
        
        // Simpan file secara fisik ke storage Laravel
        Storage::disk('local')->put($savePath, $excelContent);

        // 4. MATIKAN PERIODE & SIMPAN PATH EXCEL KE DATABASE
        $period->update([
            'is_active' => false,
            'excel_path' => $savePath
        ]);

        // 5. SETELAH EXCEL AMAN, BARU KITA SAPU BERSIH DATABASENYA!
        DailyTarget::where('period_id', $period->id)->delete();
        UsageRecap::whereBetween('date', [$startDateStr . ' 00:00:00', $endDateStr . ' 23:59:59'])->delete();
        Transaction::whereBetween('date', [$startDateStr . ' 00:00:00', $endDateStr . ' 23:59:59'])->delete();
        DailyMenu::truncate(); // Bersihkan sisa jadwal menu yang menggantung

        $this->info('Sukses! Periode berhasil ditutup otomatis, Excel tersimpan, dan DB disapu bersih.');
    }
}