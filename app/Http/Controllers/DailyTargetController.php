<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Models\DailyTarget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyTargetController extends Controller
{
    // Menampilkan halaman penyesuaian porsi dengan UI Kalender
    public function index(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();

        if (!$activePeriod) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada periode aktif. Silakan buka periode baru (14 Hari) terlebih dahulu.');
        }

        // Ambil semua target di periode ini
        $allTargets = DailyTarget::with('beneficiary')
            ->where('period_id', $activePeriod->id)
            ->orderBy('date', 'asc')
            ->get();

        // Ambil daftar tanggal unik
        $dates = $allTargets->pluck('date')->unique()->values();

        // Tentukan tanggal mana yang sedang dipilih/diklik
        $selectedDate = $request->date ?? now()->toDateString();
        if (!$dates->contains($selectedDate)) {
            $selectedDate = $dates->first();
        }

        // Susun data untuk UI Kalender 14 Hari
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

        // Tarik data target PM khusus untuk diisi di tabel bawah
        $targets = $allTargets->where('date', $selectedDate)->values();

        return view('daily-targets.index', compact('activePeriod', 'calendarData', 'selectedDate', 'targets'));
    }

    // Menyimpan perubahan porsi/libur secara massal
    public function updateBulk(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'targets' => 'required|array',
        ]);

        foreach ($request->targets as $targetId => $data) {
            $target = DailyTarget::find($targetId);
            if ($target) {
                $target->update([
                    'porsi_besar'       => $data['porsi_besar'] ?? 0,
                    'porsi_kecil'       => $data['porsi_kecil'] ?? 0,
                    'total_balita'      => $data['total_balita'] ?? 0,
                    'total_bumil_busui' => $data['total_bumil_busui'] ?? 0,
                    'is_holiday'        => isset($data['is_holiday']) ? true : false,
                ]);
            }
        }

        return back()->with('success', 'Target porsi & status libur untuk tanggal ' . Carbon::parse($request->date)->translatedFormat('d F Y') . ' berhasil diperbarui!');
    }
}