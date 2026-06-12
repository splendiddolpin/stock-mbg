<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyTarget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyTargetController extends Controller
{
    // Menampilkan halaman penyesuaian porsi dengan UI Kalender
    // Menampilkan halaman penyesuaian porsi dengan UI Kalender
    public function index(Request $request)
    {
        // 1. SISTEM INGATAN: Simpan pilihan dropdown ke memori (Session)
        if ($request->has('period_id')) {
            session(['focus_period_id' => $request->period_id]);
        }

        // 2. Tarik ingatan dari Session
        $focusId = session('focus_period_id');
        $allPeriods = Period::whereNull('excel_path')->orderBy('start_date', 'asc')->get();

        // 3. LOGIKA PINTAR: Ambil dari ingatan, jika kosong ambil yang aktif
        $activePeriod = $focusId 
            ? (Period::find($focusId) ?? Period::where('is_active', true)->first())
            : (Period::where('is_active', true)->first() ?? Period::latest()->first());

        if (!$activePeriod) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada periode sama sekali. Silakan buka periode baru (14 Hari) terlebih dahulu.');
        }

        $allTargets = DailyTarget::with('beneficiary')
            ->where('period_id', $activePeriod->id)
            ->orderBy('date', 'asc')
            ->get();

        $dates = $allTargets->pluck('date')->unique()->values();
        $selectedDate = $request->date ?? now()->toDateString();

        // Jika tanggal yang di-klik berada di luar periode terpilih, paksa ke hari pertama
        if (!$dates->contains($selectedDate)) {
            $selectedDate = $dates->first();
        }

        $calendarData = [];
        $groupedTargets = $allTargets->groupBy('date');

        foreach ($dates as $d) {
            $carbonDate = Carbon::parse($d);
            $dayTargets = $groupedTargets->get($d, collect());

            $calendarData[] = [
                'date'        => $d,
                'day_name'    => $carbonDate->translatedFormat('l'),
                'day_num'     => $carbonDate->format('d'),
                'month'       => $carbonDate->translatedFormat('M'),
                'is_sunday'   => $carbonDate->isSunday(),
                'is_selected' => $d === $selectedDate,
                'libur_count' => $dayTargets->where('is_holiday', true)->count(),
            ];
        }

        $targets = $allTargets->where('date', $selectedDate)->values();

        return view('daily-targets.index', compact('activePeriod', 'allPeriods', 'calendarData', 'selectedDate', 'targets'));
    }

    // Menyimpan perubahan porsi/libur secara massal
    // Menyimpan perubahan porsi/libur secara massal
    public function updateBulk(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'targets' => 'required|array',
        ]);

        foreach ($request->targets as $targetId => $data) {
            $target = DailyTarget::with('beneficiary')->find($targetId);
            
            if ($target) {
                $isHolidayInput = isset($data['is_holiday']) ? true : false;
                
                // --- LOGIKA AUTO-SHIFT POSYANDU ---
                // Jika PM adalah posyandu, dan sebelumnya hari ini TIDAK libur, tapi SEKARANG diliburkan
                // DAN dia punya porsi yang harusnya dikirim hari ini...
                if ($target->beneficiary->type === 'posyandu' && !$target->is_holiday && $isHolidayInput && ($target->total_balita > 0 || $target->total_bumil_busui > 0)) {
                    
                    // 1. Ambil porsinya sebelum hangus
                    $porsiBalita = $target->total_balita;
                    $porsiBumil = $target->total_bumil_busui;
                    
                    // 2. Cari hari aktif terdekat berikutnya dalam periode yang sama
                    $hariPengganti = DailyTarget::where('period_id', $target->period_id)
                        ->where('beneficiary_id', $target->beneficiary_id)
                        ->where('date', '>', $target->date)
                        ->where('is_holiday', false) // Cari yang bukan hari libur
                        ->orderBy('date', 'asc')
                        ->first();

                    // 3. Jika ketemu hari pengganti, pindahkan porsinya ke sana!
                    if ($hariPengganti) {
                        $hariPengganti->total_balita = $porsiBalita;
                        $hariPengganti->total_bumil_busui = $porsiBumil;
                        $hariPengganti->save();
                    }
                }

                // Setelah logika auto-shift aman, baru kita update data hari ininya
                // Jika libur dicentang, otomatis nol-kan semua porsi agar tidak salah belanja
                if ($isHolidayInput) {
                    $target->update([
                        'porsi_besar'       => 0,
                        'porsi_kecil'       => 0,
                        'total_balita'      => 0,
                        'total_bumil_busui' => 0,
                        'is_holiday'        => true,
                    ]);
                } else {
                    $target->update([
                        'porsi_besar'       => $data['porsi_besar'] ?? 0,
                        'porsi_kecil'       => $data['porsi_kecil'] ?? 0,
                        'total_balita'      => $data['total_balita'] ?? 0,
                        'total_bumil_busui' => $data['total_bumil_busui'] ?? 0,
                        'is_holiday'        => false,
                    ]);
                }
            }
        }

        return back()->with('success', 'Target porsi & status libur untuk tanggal ' . Carbon::parse($request->date)->translatedFormat('d F Y') . ' berhasil diperbarui! (Jadwal Posyandu yang tergeser otomatis disesuaikan).');
    }
}