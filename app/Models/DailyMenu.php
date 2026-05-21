<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMenu extends Model
{
    // Mengizinkan pengisian tanggal dan ID menu
    protected $fillable = [
        'date',
        'menu_id'
    ];

    // Membuat relasi ke tabel menus
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}