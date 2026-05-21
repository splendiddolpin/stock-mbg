<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Beneficiary;
use App\Models\Menu;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Barang Gudang
        Item::create(['name' => 'Beras Putih', 'stock_system' => 500, 'min_stock_warning' => 50, 'unit' => 'kg']);
        Item::create(['name' => 'Daging Ayam', 'stock_system' => 150, 'min_stock_warning' => 30, 'unit' => 'kg']);
        Item::create(['name' => 'Telur Ayam', 'stock_system' => 20, 'min_stock_warning' => 50, 'unit' => 'kg']); // Sengaja dibuat merah (kritis)
        Item::create(['name' => 'Sayur Sop', 'stock_system' => 100, 'min_stock_warning' => 20, 'unit' => 'kg']);

        // 2. Data Penerima Manfaat
        Beneficiary::create(['school_name' => 'SD Borobudur 1', 'total_students' => 250, 'allergen_count' => 5, 'allergen_details' => 'Alergi telur (3), Seafood (2)']);
        Beneficiary::create(['school_name' => 'SMK 1 Borobudur', 'total_students' => 450, 'allergen_count' => 12, 'allergen_details' => 'Alergi kacang (12)']);
        Beneficiary::create(['school_name' => 'SMP N 1 Mertoyudan', 'total_students' => 320, 'allergen_count' => 0, 'allergen_details' => '-']);

        // 3. Data Menu Mingguan
        Menu::create(['name' => 'Nasi Ayam Teriyaki & Sayur', 'description' => 'Menu Utama Senin']);
        Menu::create(['name' => 'Nasi Telur Dadar & Sop Macaroni', 'description' => 'Menu Utama Selasa']);
    }
}