<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCatalog extends Model
{
    protected $fillable = ['category', 'name', 'price'];
}