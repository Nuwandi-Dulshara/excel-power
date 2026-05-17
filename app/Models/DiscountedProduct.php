<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountedProduct extends Model
{
    protected $fillable = [
        'product_variant_id',
        'current_quantity',
        'discount_quantity',
        'price_received',
        'our_price',
        'supplier_name',
        'discount_percentage',
        'maximum_allowed_discount_percentage',
        'discount_price',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'reason',
        'status',
    ];

    protected $casts = [
        'price_received' => 'decimal:2',
        'our_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'maximum_allowed_discount_percentage' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
