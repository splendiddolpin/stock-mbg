<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffCash extends Model
{
    protected $fillable = ['date', 'type', 'amount', 'description'];
}