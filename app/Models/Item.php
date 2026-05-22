<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'stock_system', 'hpp', 'min_stock_warning', 'unit'
    ];

    // Logika Status Kata
    public function getStatusAttribute()
    {
        if ($this->stock_system <= 0) return 'Habis';
        if ($this->stock_system <= $this->min_stock_warning) return 'Hampir Habis';
        return 'Aman';
    }

    // Logika Waritemna Tailwind (Badge)
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Habis' => 'bg-red-100 text-red-800 border-red-300',
            'Hampir Habis' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            default => 'bg-green-100 text-green-800 border-green-300',
        };
    }
    public function transactions() {
    return $this->hasMany(Transaction::class);
    }
}