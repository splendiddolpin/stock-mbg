<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'menu_ingredients')
                    // INI DIA KUNCI JAWABANNYA! KITA HARUS SEBUTKAN KE-4 GRAMASINYA DI SINI:
                    ->withPivot('gramasi_besar', 'gramasi_kecil', 'gramasi_balita', 'gramasi_bumil')
                    ->withTimestamps();
    }

    // Menambahkan fungsi show untuk melihat detail menu & meracik resep
    public function show(Menu $menu)
    {
        // Kita panggil juga semua data Items (Bahan) untuk pilihan di dropdown form
        $items = \App\Models\Item::orderBy('name', 'asc')->get();
        
        return view('menus.show', compact('menu', 'items'));
    }
}