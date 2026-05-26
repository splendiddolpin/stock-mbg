<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRequest extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'menu_name', 'notes', 'status'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}