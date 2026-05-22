<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'school_name',
        'porsi_besar',
        'porsi_kecil',
        'allergen_count',
        'allergen_details',
        'type',
        'total_balita',
        'total_bumil_busui',
    ];

    // Fungsi bantuan agar dashboard tetap bisa memanggil 'total_students'
    public function getTotalStudentsAttribute()
    {
        return $this->porsi_besar + $this->porsi_kecil;
    }
    
    // Relasi untuk mendata bahan baku apa saja yang menjadi alergen
    public function allergens()
    {
        return $this->belongsToMany(Item::class, 'allergen_item')
                    ->withPivot('anak_count') // <-- Tambahkan baris ini
                    ->withTimestamps();
    }
}