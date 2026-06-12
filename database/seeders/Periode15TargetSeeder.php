<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;
use App\Models\DailyTarget;
use App\Models\Beneficiary;

class Periode15TargetSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil Periode 15 yang PALING BARU (Mencegah salah masuk ke periode 15 lama)
        $period = Period::where('name', 'like', '%15%')->orderBy('id', 'desc')->first();

        if (!$period) {
            $this->command->error('❌ Gagal! Tidak ditemukan periode 15.');
            return;
        }

        $this->command->info("🔍 DIAGNOSIS DIMULAI: Fokus pada {$period->name} (ID: {$period->id})");

        // 2. CEK TANGGAL 16 JUNI (LIBUR)
        // Pakai 'like' berjaga-jaga jika format tanggal di DB Anda ada jamnya (cth: 2026-06-16 00:00:00)
        $target16 = DailyTarget::where('period_id', $period->id)->where('date', 'like', '2026-06-16%')->count();
        $this->command->warn(">> Ditemukan {$target16} baris jadwal di tanggal 16 Juni.");
        
        $updateLibur = DailyTarget::where('period_id', $period->id)->where('date', 'like', '2026-06-16%')
            ->update(['porsi_besar' => 0, 'porsi_kecil' => 0, 'total_balita' => 0, 'total_bumil_busui' => 0, 'is_holiday' => true]);
        
        $this->command->info("✅ Berhasil meliburkan {$updateLibur} baris instansi.");

        // 3. CEK NAMA SEKOLAH
        $smk1 = Beneficiary::where('school_name', 'like', '%SMK 1%')->first();
        $smk2 = Beneficiary::where('school_name', 'like', '%SMK 2%')->first();

        if (!$smk1) $this->command->error("❌ Gagal! Sekolah SMK 1 tidak ditemukan di DB.");
        if (!$smk2) $this->command->error("❌ Gagal! Sekolah SMK 2 tidak ditemukan di DB.");

        if ($smk1) {
            $updateSMK1 = DailyTarget::where('period_id', $period->id)
                ->where('beneficiary_id', $smk1->id)
                ->where(function($q) {
                    $q->where('date', 'like', '2026-06-15%')->orWhere('date', 'like', '2026-06-17%');
                })
                ->update(['porsi_besar' => 162]);
            $this->command->info("✅ Berhasil mengubah {$updateSMK1} baris jadwal SMK 1.");
        }

        if ($smk2) {
            $updateSMK2 = DailyTarget::where('period_id', $period->id)
                ->where('beneficiary_id', $smk2->id)
                ->where(function($q) {
                    $q->where('date', 'like', '2026-06-15%')->orWhere('date', 'like', '2026-06-17%')->orWhere('date', 'like', '2026-06-18%');
                })
                ->update(['porsi_besar' => 160]);
            $this->command->info("✅ Berhasil mengubah {$updateSMK2} baris jadwal SMK 2.");
        }
        
        $this->command->info("🏁 DIAGNOSIS SELESAI.");
    }
}