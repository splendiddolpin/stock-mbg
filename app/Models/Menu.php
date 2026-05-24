<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // 1. Izinkan kolom name dan description untuk diisi
    protected $fillable = [
        'name',
        'description',
    ];

    // 2. Relasi ke Bahan Baku (Items)
    public function items()
    {
        return $this->belongsToMany(Item::class, 'menu_ingredients')
                    // Cukup 2 gramasi ini saja sesuai pembaruan Ahli Gizi kita
                    ->withPivot('gramasi_besar', 'gramasi_kecil')
                    ->withTimestamps();
    }
}