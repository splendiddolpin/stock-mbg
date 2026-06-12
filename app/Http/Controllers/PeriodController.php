<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyTarget;
use App\Models\Beneficiary;
use App\Models\DailyMenu;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PeriodController extends Controller
{
    // 1. Menampilkan Daftar Sejarah Periode (Index)
    public function index()
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        return view('periods.index', compact('periods'));
    }

    // 2. Menampilkan Form Tambah Periode (Create)
    // 2. Menampilkan Form Tambah Periode (Bebas Buat Kapan Saja)
    public function create()
    {
        return view('periods.create');
    }

    // 3. Menyimpan Periode Baru & Generate Otomatis Kalender Target Harian
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays(13); // Otomatis 14 hari penuh
        $today = Carbon::today();

        // Cek apakah tanggal mulai periode ini adalah hari ini atau sebelumnya
        // Jika ya, langsung aktifkan. Jika untuk masa depan, biarkan tertidur (false)
        $isActive = $startDate->lte($today) && $endDate->gte($today);

        $period = Period::create([
            'name' => $request->name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_active' => $isActive,
        ]);

        $beneficiaries = Beneficiary::all();
        $hutangPosyandu = [];
        
        foreach ($beneficiaries->where('type', 'posyandu') as $pm) {
            $hutangPosyandu[$pm->id] = false;
        }

        // Generate Kalender Harian seperti biasa...
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $isSunday = $date->isSunday();
            $dateString = $date->toDateString();

            foreach ($beneficiaries as $pm) {
                $isHoliday = false;
                $pBesar  = $pm->porsi_besar ?? 0;
                $pKecil  = $pm->porsi_kecil ?? 0;
                $tBalita = $pm->total_balita ?? 0;
                $tBumil  = $pm->total_bumil_busui ?? 0;

                if ($isSunday) {
                    $pBesar = $pKecil = $tBalita = $tBumil = 0;
                    $isHoliday = true;
                } else {
                    if ($pm->type === 'sekolah') {
                        if ($date->isSaturday()) {
                            $pBesar = $pKecil = 0;
                            $isHoliday = true;
                        }
                    } 
                    else if ($pm->type === 'posyandu') {
                        $jadwalNormal = $date->isMonday() || $date->isThursday();
                        $harusKirimHariIni = $jadwalNormal || $hutangPosyandu[$pm->id];

                        if ($harusKirimHariIni) {
                            $hutangPosyandu[$pm->id] = false; 
                        } else {
                            $tBalita = $tBumil = 0;
                        }
                    }
                }

                if ($isSunday && $pm->type === 'posyandu') {
                    $jadwalNormal = $date->isMonday() || $date->isThursday();
                    if ($jadwalNormal || $hutangPosyandu[$pm->id]) {
                        $hutangPosyandu[$pm->id] = true;
                    }
                }

                DailyTarget::create([
                    'period_id'         => $period->id,
                    'date'              => $dateString,
                    'beneficiary_id'    => $pm->id,
                    'porsi_besar'       => $pBesar,
                    'porsi_kecil'       => $pKecil,
                    'total_balita'      => $tBalita,
                    'total_bumil_busui' => $tBumil,
                    'is_holiday'        => $isHoliday,
                ]);
            }
        }

        return redirect()->route('periods.index')->with('success', 'Jadwal Periode (14 Hari) berhasil ditambahkan ke kalender sistem!');
    }

    // 4. Tutup Buku Periode Aktif (Close)
    public function closePeriod()
    {
        $activePeriod = Period::where('is_active', true)->first();
        
        if ($activePeriod) {
            $activePeriod->update(['is_active' => false]);
            DailyMenu::truncate(); // Bersihkan sisa jadwal besok/hari ini yang menggantung

            return redirect()->route('periods.index')->with('success', 'Periode berhasil ditutup! Data aman diarsipkan (Siap disapu DB).');
        }

        return redirect()->route('periods.index')->with('error', 'Tidak ada periode aktif yang bisa ditutup.');
    }

    // 5. Fitur Preview & Download Arsip (Kebal ERR_INVALID_RESPONSE)
    // 5. Fitur Preview & Download Arsip (Kebal ERR_INVALID_RESPONSE)
    public function exportExcel(Period $period)
    {
        $safeName = preg_replace('/[^A-Za-z0-9\-\._]/', '_', $period->name ?? 'Periode_' . $period->id);

        // =================================================================
        // SKENARIO 1: JIKA PERIODE SUDAH DIARSIPKAN (AMBIL FILE FISIK)
        // =================================================================
        if ($period->excel_path) {
            $excelPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $period->excel_path);
            
            $pathPrivate = storage_path('app/private' . DIRECTORY_SEPARATOR . $excelPath);
            $pathPublic = storage_path('app' . DIRECTORY_SEPARATOR . $excelPath);

            $targetFile = null;
            if (file_exists($pathPrivate)) {
                $targetFile = $pathPrivate;
            } elseif (file_exists($pathPublic)) {
                $targetFile = $pathPublic;
            }

            if ($targetFile) {
                $downloadName = "Arsip_Final_MBG_{$safeName}.xls";
                return response()->download($targetFile, $downloadName);
            }

            // Jika database bilang sudah diarsipkan tapi file fisiknya hilang
            return redirect()->route('archives.index')->with('error', 'File fisik untuk periode ini tidak ditemukan di server.');
        }

        // =================================================================
        // SKENARIO 2: JIKA PERIODE MASIH AKTIF / PREVIEW (AMBIL DARI DATABASE)
        // =================================================================
        $excelContent = view('periods.export', compact('period'))->render();
        $downloadName = "Preview_MBG_{$safeName}.xls";

        return response($excelContent)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $downloadName . '"');
    }

    // 6. Arsipkan & Sapu Database Secara Permanen
    public function destroy(Period $period)
    {
        if ($period->is_active) {
            return redirect()->back()->with('error', 'Periode yang sedang aktif tidak boleh diarsipkan! Tutup periode terlebih dahulu.');
        }

        if ($period->excel_path) {
            return redirect()->back()->with('error', 'Periode ini sudah pernah diarsipkan dan dibersihkan dari database.');
        }

        $startDate = Carbon::parse($period->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($period->end_date)->format('Y-m-d');
        $periodName = Str::slug($period->name, '_');
        $fileName = "Arsip_Matang_MBG_{$periodName}_FROZEN.xls";

        // LANGKAH KRITIS: Render isi Excel saat data di database MASIH UTUH
        $excelContent = view('periods.export', compact('period'))->render();

        // Simpan file fisiknya ke server lokal (Dibekukan selamanya)
        $savePath = "excel_archives/{$fileName}";
        Storage::disk('local')->put($savePath, $excelContent);

        // Tandai alamat file fisiknya di database
        $period->update(['excel_path' => $savePath]);
        
        // SEKARANG BARU AMAN UNTUK DISAPU BERSIH! (DB Menjadi sangat enteng)
        $period->dailyTargets()->delete();
        \App\Models\UsageRecap::whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->delete();
        \App\Models\Transaction::whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->delete();

        return redirect()->route('periods.index')->with('success', 'Sistem Arsip Sukses! File Excel telah dibekukan di server, dan database berhasil disapu bersih.');
    }
}