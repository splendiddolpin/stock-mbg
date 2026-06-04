<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyTarget;
use App\Models\Beneficiary;
use App\Models\DailyMenu;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeriodController extends Controller
{
    // 1. Menampilkan Daftar Sejarah Periode (Index)
    public function index()
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        return view('periods.index', compact('periods'));
    }

    // 2. Menampilkan Form Tambah Periode (Create)
    public function create()
    {
        $activePeriod = Period::where('is_active', true)->first();
        if ($activePeriod) {
            return redirect()->route('periods.index')->with('error', 'Tidak bisa membuat periode baru karena Periode "' . $activePeriod->name . '" masih aktif! Tutup periode tersebut terlebih dahulu.');
        }

        return view('periods.create');
    }

    // 3. Menyimpan Periode Baru & Generate Otomatis Kalender Target Harian (Store)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        $activePeriod = Period::where('is_active', true)->first();
        if ($activePeriod) {
            return redirect()->back()->with('error', 'Masih ada periode yang aktif. Tutup terlebih dahulu.');
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addDays(13); // Otomatis 14 hari penuh

        $period = Period::create([
            'name' => $request->name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_active' => true,
        ]);

        $beneficiaries = Beneficiary::all();

        // Siapkan Sistem Hutang Jadwal Posyandu
        $hutangPosyandu = [];
        foreach ($beneficiaries->where('type', 'posyandu') as $pm) {
            $hutangPosyandu[$pm->id] = false;
        }

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
                        // Sekolah libur di hari Sabtu
                        if ($date->isSaturday()) {
                            $pBesar = $pKecil = 0;
                            $isHoliday = true;
                        }
                    } 
                    else if ($pm->type === 'posyandu') {
                        $jadwalNormal = $date->isMonday() || $date->isThursday();
                        $harusKirimHariIni = $jadwalNormal || $hutangPosyandu[$pm->id];

                        if ($harusKirimHariIni) {
                            // Eksekusi pengiriman
                            $hutangPosyandu[$pm->id] = false; // Lunasi hutang
                        } else {
                            // Bukan jadwalnya
                            $tBalita = $tBumil = 0;
                        }
                    }
                }

                // Cek: Jika ini hari Minggu dan Posyandu harusnya jadwal kirim, jadikan hutang!
                if ($isSunday && $pm->type === 'posyandu') {
                    $jadwalNormal = $date->isMonday() || $date->isThursday(); // Logikanya gak mungkin Senin karena ini cek hari Minggu, tapi aman untuk custom holiday nanti
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

        return redirect()->route('periods.index')->with('success', 'Periode baru (14 Hari) berhasil dibuka dan kalender target harian telah di-generate!');
    }

    // 4. Tutup Buku Periode Aktif (Close)
    public function closePeriod()
    {
        $activePeriod = Period::where('is_active', true)->first();
        
        if ($activePeriod) {
            $activePeriod->update(['is_active' => false]);
            DailyMenu::truncate(); // Bersihkan sisa jadwal besok/hari ini yang menggantung

            return redirect()->route('periods.index')->with('success', 'Periode berhasil ditutup! Data aman diarsipkan.');
        }

        return redirect()->route('periods.index')->with('error', 'Tidak ada periode aktif yang bisa ditutup.');
    }

    // 5. Menghapus Periode sekaligus Export Excel (Destroy & Export)
    public function destroy(Period $period)
    {
        if ($period->is_active) {
            return redirect()->back()->with('error', 'Periode yang sedang aktif tidak boleh dihapus langsung! Tutup periode terlebih dahulu.');
        }

        // 1. SIAPKAN DATA (Ambil berdasarkan rentang tanggal periode)
        $periodName = str_replace(' ', '_', $period->name);
        $fileName = "Arsip_Lengkap_MBG_{$periodName}.xls";
        $startDate = $period->start_date;
        $endDate = $period->end_date;

        $dataTargetHarian = $period->dailyTargets()->with('beneficiary')->orderBy('date', 'asc')->get();
        
        // Ambil data Barang Keluar (UsageRecap)
        $dataBarangKeluar = \App\Models\UsageRecap::with(['item', 'menu'])
            ->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
            
        // Ambil data Barang Masuk (Transaction tipe 'in')
        $dataBarangMasuk = \App\Models\Transaction::with('item')
            ->where('type', 'in')
            ->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();

        // 2. PEMUSNAHAN MASSAL DARI DATABASE
        $period->dailyTargets()->delete();
        \App\Models\UsageRecap::whereBetween('date', [$startDate, $endDate])->delete();
        \App\Models\Transaction::whereBetween('date', [$startDate, $endDate])->delete();
        $period->delete();

        // 3. GENERATE BLADE VIEW MENJADI FILE EXCEL
        return response()->streamDownload(function() use($period, $dataTargetHarian, $dataBarangKeluar, $dataBarangMasuk) {
            echo view('periods.export', compact('period', 'dataTargetHarian', 'dataBarangKeluar', 'dataBarangMasuk'))->render();
        }, $fileName, [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ]);
    }
}