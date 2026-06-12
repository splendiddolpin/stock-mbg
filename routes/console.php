<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Period;
use App\Models\DailyTarget;
use App\Models\UsageRecap;
use App\Models\Transaction;
use App\Models\DailyMenu;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// =========================================================================
// ROBOT MBG: OTOMATIS TUTUP PERIODE & SAPU DB
// =========================================================================
// =========================================================================
// ROBOT MANAJER MBG: MENGATUR SHIFT PERIODE SETIAP MALAM
// =========================================================================
Artisan::command('mbg:auto-close-period', function () {
    $today = Carbon::today()->format('Y-m-d');

    // TUGAS 1: CARI PERIODE KADALUWARSA & SAPU BERSIH!
    // Syarat: end_date sudah lewat dari hari ini AND excel_path masih kosong
    $expiredPeriods = Period::where('end_date', '<', $today)
                            ->whereNull('excel_path')
                            ->get();

    foreach ($expiredPeriods as $period) {
        $startDateStr = Carbon::parse($period->start_date)->format('Y-m-d');
        $endDateStr = Carbon::parse($period->end_date)->format('Y-m-d');
        
        $periodName = Str::slug($period->name ?? 'Periode_'.$period->id, '_');
        $fileName = "Arsip_Matang_MBG_{$periodName}_FROZEN.xls";
        $savePath = "excel_archives/{$fileName}";

        // Cetak Excel
        $excelContent = view('periods.export', compact('period'))->render();
        Storage::disk('local')->put($savePath, $excelContent);

        // Matikan & Catat Alamatnya
        $period->update([
            'is_active' => false,
            'excel_path' => $savePath
        ]);

        // Sapu Bersih DB Khusus Periode Ini Saja
        DailyTarget::where('period_id', $period->id)->delete();
        UsageRecap::whereBetween('date', [$startDateStr . ' 00:00:00', $endDateStr . ' 23:59:59'])->delete();
        Transaction::whereBetween('date', [$startDateStr . ' 00:00:00', $endDateStr . ' 23:59:59'])->delete();
        DailyMenu::truncate();

        $this->info("Berhasil mengarsipkan dan membersihkan: " . $period->name);
    }

    // TUGAS 2: AKTIFKAN PERIODE HARI INI (Shift Baru)
    // Syarat: Hari ini masuk dalam rentang start_date dan end_date
    Period::where('start_date', '<=', $today)
          ->where('end_date', '>=', $today)
          ->update(['is_active' => true]);

    // Matikan periode masa depan yang mungkin tidak sengaja aktif
    Period::where('start_date', '>', $today)
          ->update(['is_active' => false]);

    $this->info('Sukses! Robot selesai merapikan shift periode untuk hari ini.');
})->purpose('Manajer otomatis untuk mengaktifkan dan mengarsipkan periode');

// Jadwalkan robot berjalan setiap hari jam 00:01 malam
Schedule::command('mbg:auto-close-period')->dailyAt('00:01');