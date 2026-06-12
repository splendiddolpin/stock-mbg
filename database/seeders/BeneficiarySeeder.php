<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeneficiarySeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $beneficiaries = [
            // ==========================================
            // 1. KATEGORI: INSTANSI SEKOLAH
            // ==========================================
            [
                'school_name' => 'SMK 1',
                'type' => 'sekolah',
                'porsi_besar' => 266,
                'porsi_kecil' => 0,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],
            [
                'school_name' => 'SMK 2',
                'type' => 'sekolah',
                'porsi_besar' => 279,
                'porsi_kecil' => 0,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],
            [
                'school_name' => 'SMP',
                'type' => 'sekolah',
                'porsi_besar' => 106,
                'porsi_kecil' => 0,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],
            [
                'school_name' => 'SMA',
                'type' => 'sekolah',
                'porsi_besar' => 119,
                'porsi_kecil' => 0,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],
            [
                'school_name' => 'SD (Besar & Kecil)',
                'type' => 'sekolah',
                'porsi_besar' => 183, // Dibagi dari total 366
                'porsi_kecil' => 183,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],
            [
                'school_name' => 'TK',
                'type' => 'sekolah',
                'porsi_besar' => 0,
                'porsi_kecil' => 122,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],
            [
                'school_name' => 'KB',
                'type' => 'sekolah',
                'porsi_besar' => 0,
                'porsi_kecil' => 50,
                'total_balita' => 0,
                'total_bumil_busui' => 0,
            ],

            // ==========================================
            // 2. KATEGORI: POSYANDU (3B)
            // ==========================================
            [
                'school_name' => 'Sambeng',
                'type' => 'posyandu',
                'porsi_besar' => 0,
                'porsi_kecil' => 0,
                'total_balita' => 56, // Sesuai data Excel lama (Total 84)
                'total_bumil_busui' => 28,
            ],
            [
                'school_name' => 'Posyandu Borobudur',
                'type' => 'posyandu',
                'porsi_besar' => 0,
                'porsi_kecil' => 0,
                'total_balita' => 322, // Estimasi rasio 2:1 dari total target 483
                'total_bumil_busui' => 161, 
            ],
        ];

        // Tambahkan timestamp (created_at & updated_at) otomatis ke setiap data
        foreach ($beneficiaries as &$ben) {
            $ben['created_at'] = $now;
            $ben['updated_at'] = $now;
        }

        // Masukkan data ke dalam database
        DB::table('beneficiaries')->insert($beneficiaries);
    }
}