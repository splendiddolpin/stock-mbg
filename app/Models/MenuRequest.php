<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuRequest extends Model
{
    protected $fillable = ['beneficiary_id', 'student_name', 'menu_name', 'reason', 'status'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}