<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMenu extends Model
{
    use HasFactory;

    // INI KUNCI JAWABANNYA! KITA IZINKAN TARGET TYPE DISIMPAN
    protected $fillable = [
        'date',
        'menu_id',
        'target_type',
    ];

    // Relasi ke Master Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}