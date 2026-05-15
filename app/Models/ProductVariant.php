<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DiscountedProduct;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_name',
        'product_unit_id',
        'size',
        'barcode',
        'sku',
        'description',
        'purchase_price',
        'purchase_quantity',
        'cost_price',
        'selling_price',
        'our_price',
        'minimum_wholesale_quantity',
        'wholesale_price',
        'opening_stock',
        'current_stock',
        'reorder_level',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'our_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

        public function discountedProducts()
    {
        return $this->hasMany(DiscountedProduct::class);
    }
    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }
}