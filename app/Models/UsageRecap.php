<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageRecap extends Model
{
    protected $fillable = ['date', 'item_id', 'menu_id', 'quantity_out', 'unit', 'total_cost'];

    public function item() { return $this->belongsTo(Item::class); }
    public function menu() { return $this->belongsTo(Menu::class); }
}
