<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // HPP dihapus dari sini
    protected $fillable = [
        'period_id', 'item_id', 'type', 'quantity', 'date', 'description'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}