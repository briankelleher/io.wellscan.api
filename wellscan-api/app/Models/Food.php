<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $table = "food";
    protected $fillable = ['name', 'upc', 'nutrition', 'rankings', 'status', 'nutrition_source', 'nutrition_method' ];
    protected $casts = [
        'nutrition' => 'json',
        'rankings' => 'json',
    ];
}
