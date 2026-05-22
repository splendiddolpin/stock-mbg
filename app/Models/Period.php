<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    // Relasi lama (jangan dihapus)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // --- RELASI BARU KITA ---
    // 1 Periode punya BANYAK Target Harian (Snapshot PM)
    public function dailyTargets()
    {
        return $this->hasMany(DailyTarget::class);
    }
}