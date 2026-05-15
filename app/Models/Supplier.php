<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_name',
        'supplier_code',
        'contact_person',
        'phone',
        'email',
        'address',
        'company_name',
        'br_number',
        'vat_number',
        'opening_balance',
        'payment_terms',
        'notes',
        'status',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];
}