<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTarget extends Model
{
    protected $fillable = [
        'period_id', 'date', 'beneficiary_id', 
        'porsi_besar', 'porsi_kecil', 
        'total_balita', 'total_bumil_busui', 'is_holiday'
    ];

    // Relasi ke Periode dan Penerima Manfaat
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}