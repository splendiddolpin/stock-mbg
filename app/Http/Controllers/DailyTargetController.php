<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Period;
use App\Models\DailyTarget;
use Carbon\Carbon;

class DailyTargetController extends Controller
{
    public function index()
    {
        // 1. Cari periode yang sedang aktif saat ini
        $period = Period::where('is_active', true)->latest()->first();

        if (!$period) {
            return redirect()->route('dashboard')->with('warning', 'Belum ada periode yang aktif. Silakan buat Periode baru terlebih dahulu.');
        }

        // 2. Ambil semua target harian di periode ini, gabungkan dengan data Penerima, lalu kelompokkan per Tanggal
        $dailyTargets = DailyTarget::with('beneficiary')
            ->where('period_id', $period->id)
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('date');

        return view('daily-targets.index', compact('period', 'dailyTargets'));
    }

    public function update(Request $request)
    {
        // Tangkap semua inputan dari tabel
        $targets = $request->input('targets', []);

        foreach ($targets as $id => $data) {
            $target = DailyTarget::find($id);
            if ($target) {
                // Update datanya
                $target->update([
                    'porsi_besar'       => $data['porsi_besar'] ?? 0,
                    'porsi_kecil'       => $data['porsi_kecil'] ?? 0,
                    'total_balita'      => $data['total_balita'] ?? 0,
                    'total_bumil_busui' => $data['total_bumil_busui'] ?? 0,
                    // Jika checkbox libur dicentang, maka is_holiday = true
                    'is_holiday'        => isset($data['is_holiday']) ? true : false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Perubahan target harian dan jadwal libur berhasil disimpan!');
    }
}