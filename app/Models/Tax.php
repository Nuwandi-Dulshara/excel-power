<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'tax_name',
        'tax_rate',
        'tax_type',
        'status',
        'description',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
    ];
}