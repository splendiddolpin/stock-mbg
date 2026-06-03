<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCatalog;

class MenuCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ==========================================
            // KATEGORI: KARBOHIDRAT (22 Item)
            // ==========================================
            ['category' => 'Karbohidrat', 'name' => 'Nasi Putih', 'price' => 1500],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Uduk', 'price' => 2500],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Daun Jeruk', 'price' => 1700],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Onigiri', 'price' => 2500],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Sushi/Kimbab', 'price' => 2500],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Kuning', 'price' => 2000],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Biru', 'price' => 2000],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Ungu', 'price' => 2000],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Hijau', 'price' => 2000],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Goreng', 'price' => 2500],
            ['category' => 'Karbohidrat', 'name' => 'Nasi Kebuli', 'price' => 3000],
            ['category' => 'Karbohidrat', 'name' => 'Kentang Rebus', 'price' => 2800],
            ['category' => 'Karbohidrat', 'name' => 'Potato Wedges', 'price' => 3000],
            ['category' => 'Karbohidrat', 'name' => 'Kentang Perkedel', 'price' => 3000],
            ['category' => 'Karbohidrat', 'name' => 'Ubi Ungu Rebus', 'price' => 2500],
            ['category' => 'Karbohidrat', 'name' => 'Ubi Ungu Cream Cheese', 'price' => 3400],
            ['category' => 'Karbohidrat', 'name' => 'Ubi Cilembu', 'price' => 3000],
            ['category' => 'Karbohidrat', 'name' => 'Singkong Rebus', 'price' => 2000],
            ['category' => 'Karbohidrat', 'name' => 'Mie Telur', 'price' => 2000],
            ['category' => 'Karbohidrat', 'name' => 'Mie Ramen', 'price' => 2200],
            ['category' => 'Karbohidrat', 'name' => 'Mie Spaghetti', 'price' => 2200],
            ['category' => 'Karbohidrat', 'name' => 'Jagung Rebus', 'price' => 3500],

            // ==========================================
            // KATEGORI: PROTEIN HEWANI (32 Item)
            // ==========================================
            ['category' => 'Protein Hewani', 'name' => 'Ayam Goreng Kremes', 'price' => 4000],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Lengkuas', 'price' => 4000],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Geprek', 'price' => 4300],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Saos Teriyaki', 'price' => 4500],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Kecap Wijen', 'price' => 4300],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Saos Barbeque', 'price' => 4500],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Pop', 'price' => 4300],
            ['category' => 'Protein Hewani', 'name' => 'Ayam Katsu', 'price' => 4700],
            ['category' => 'Protein Hewani', 'name' => 'Chicken Wings BBQ', 'price' => 4300],
            ['category' => 'Protein Hewani', 'name' => 'Sate Ayam', 'price' => 4500],
            ['category' => 'Protein Hewani', 'name' => 'Sop Ayam', 'price' => 4500],
            ['category' => 'Protein Hewani', 'name' => 'Ikan Lele Goreng', 'price' => 4700],
            ['category' => 'Protein Hewani', 'name' => 'Ikan Lele Mangut', 'price' => 5000],
            ['category' => 'Protein Hewani', 'name' => 'Ikan Bandeng Goreng', 'price' => 3500],
            ['category' => 'Protein Hewani', 'name' => 'Ikan Dori', 'price' => 4000],
            ['category' => 'Protein Hewani', 'name' => 'Daging Sapi Rendang', 'price' => 4700],
            ['category' => 'Protein Hewani', 'name' => 'Sop Daging Sapi', 'price' => 4500],
            ['category' => 'Protein Hewani', 'name' => 'Abon Ayam', 'price' => 4000],
            ['category' => 'Protein Hewani', 'name' => 'Abon Sapi', 'price' => 6000],
            ['category' => 'Protein Hewani', 'name' => 'Abon Ikan', 'price' => 4500],
            ['category' => 'Protein Hewani', 'name' => 'Telur Ayam Rebus', 'price' => 2500],
            ['category' => 'Protein Hewani', 'name' => 'Telur Mata Sapi', 'price' => 2500],
            ['category' => 'Protein Hewani', 'name' => 'Telur Dadar', 'price' => 2000],
            ['category' => 'Protein Hewani', 'name' => 'Telur Saos Barbeque', 'price' => 3000],
            ['category' => 'Protein Hewani', 'name' => 'Telur Asam Manis', 'price' => 3000],
            ['category' => 'Protein Hewani', 'name' => 'Scramble Egg', 'price' => 2500],
            ['category' => 'Protein Hewani', 'name' => 'Telur Balado', 'price' => 3000],
            ['category' => 'Protein Hewani', 'name' => 'Telur Ceplok Bolognese', 'price' => 3000],
            ['category' => 'Protein Hewani', 'name' => 'Telur Asin', 'price' => 4000],
            ['category' => 'Protein Hewani', 'name' => 'Telur Puyuh Rebus', 'price' => 2500],
            ['category' => 'Protein Hewani', 'name' => 'Telur Puyuh Kecap', 'price' => 3000],
            ['category' => 'Protein Hewani', 'name' => 'Bakso Saos Tiram', 'price' => 3000],

            // ==========================================
            // KATEGORI: PROTEIN NABATI (10 Item)
            // ==========================================
            ['category' => 'Protein Nabati', 'name' => 'Tempe Garit', 'price' => 1000],
            ['category' => 'Protein Nabati', 'name' => 'Kering Tempe', 'price' => 1200],
            ['category' => 'Protein Nabati', 'name' => 'Tempe Krispi', 'price' => 1200],
            ['category' => 'Protein Nabati', 'name' => 'Tahu', 'price' => 1000],
            ['category' => 'Protein Nabati', 'name' => 'Tahu Krispi', 'price' => 1200],
            ['category' => 'Protein Nabati', 'name' => 'Edamame', 'price' => 1500],
            ['category' => 'Protein Nabati', 'name' => 'Kacang Merah', 'price' => 1500],
            ['category' => 'Protein Nabati', 'name' => 'Kacang Polong', 'price' => 1200],
            ['category' => 'Protein Nabati', 'name' => 'Kacang Kapri', 'price' => 1000],
            ['category' => 'Protein Nabati', 'name' => 'Kacang Hijau', 'price' => 1300],

            // ==========================================
            // KATEGORI: SAYUR (20 Item) - BARU DITAMBAHKAN
            // ==========================================
            ['category' => 'Sayur', 'name' => 'Mix Vegetable', 'price' => 1700],
            ['category' => 'Sayur', 'name' => 'Pakcoy', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Bayam', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Sawi Hijau', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Sawi Putih', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Brokoli', 'price' => 800],
            ['category' => 'Sayur', 'name' => 'Kembang Kol', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Kubis', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Jamur', 'price' => 600],
            ['category' => 'Sayur', 'name' => 'Rumput Laut', 'price' => 600],
            ['category' => 'Sayur', 'name' => 'Selada', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Kemangi', 'price' => 400],
            ['category' => 'Sayur', 'name' => 'Tomat', 'price' => 400],
            ['category' => 'Sayur', 'name' => 'Timun', 'price' => 400],
            ['category' => 'Sayur', 'name' => 'Daun Singkong', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Tauge', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Kol Ungu', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Kacang Panjang', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Buncis', 'price' => 500],
            ['category' => 'Sayur', 'name' => 'Wortel', 'price' => 500],

            // ==========================================
            // KATEGORI: BUAH (25 Item)
            // ==========================================
            ['category' => 'Buah', 'name' => 'Semangka', 'price' => 1200],
            ['category' => 'Buah', 'name' => 'Melon', 'price' => 1400],
            ['category' => 'Buah', 'name' => 'Buah Naga', 'price' => 1700],
            ['category' => 'Buah', 'name' => 'Apel Candy', 'price' => 3000],
            ['category' => 'Buah', 'name' => 'Apel Fuji', 'price' => 4000],
            ['category' => 'Buah', 'name' => 'Apel Hijau', 'price' => 2000],
            ['category' => 'Buah', 'name' => 'Jeruk Medan', 'price' => 2800],
            ['category' => 'Buah', 'name' => 'Jeruk Santang', 'price' => 3000],
            ['category' => 'Buah', 'name' => 'Jeruk Baby', 'price' => 2300],
            ['category' => 'Buah', 'name' => 'Pear', 'price' => 4200],
            ['category' => 'Buah', 'name' => 'Jambu Kristal', 'price' => 3000],
            ['category' => 'Buah', 'name' => 'Pisang Ambon', 'price' => 2400],
            ['category' => 'Buah', 'name' => 'Pisang Raja', 'price' => 2400],
            ['category' => 'Buah', 'name' => 'Pisang Kapasan', 'price' => 1000],
            ['category' => 'Buah', 'name' => 'Belimbing', 'price' => 3000],
            ['category' => 'Buah', 'name' => 'Anggur Merah', 'price' => 4000],
            ['category' => 'Buah', 'name' => 'Anggur Muscat', 'price' => 4500],
            ['category' => 'Buah', 'name' => 'Kelengkeng', 'price' => 4000],
            ['category' => 'Buah', 'name' => 'Manggis', 'price' => 2500],
            ['category' => 'Buah', 'name' => 'Mangga', 'price' => 3000],
            ['category' => 'Buah', 'name' => 'Strawberry', 'price' => 2000],
            ['category' => 'Buah', 'name' => 'Kurma', 'price' => 2000],
            ['category' => 'Buah', 'name' => 'Rambutan', 'price' => 1500],
            ['category' => 'Buah', 'name' => 'Kiwi', 'price' => 2000],
            ['category' => 'Buah', 'name' => 'Alpukat', 'price' => 2000],

            // ==========================================
            // KATEGORI: SUSU (2 Item) - BARU DITAMBAHKAN
            // ==========================================
            ['category' => 'Susu', 'name' => 'Susu Ultra Milk', 'price' => 3500],
            ['category' => 'Susu', 'name' => 'Susu Greenfield', 'price' => 3500],
        ];

        foreach ($data as $item) {
            MenuCatalog::updateOrCreate(
                ['name' => $item['name']], 
                [
                    'category' => $item['category'],
                    'price' => $item['price']
                ]
            );
        }
    }
}