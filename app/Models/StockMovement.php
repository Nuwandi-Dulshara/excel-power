<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public const TYPES = [
        'initial_stock',
        'stock_in',
        'stock_out',
        'sale',
        'sale_return',
        'purchase_return',
        'damaged',
        'expired',
        'adjustment',
        'discount_reserved',
        'discount_sold',
    ];

    protected $fillable = [
        'product_variant_id',
        'product_id',
        'supplier_id',
        'movement_type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'reason',
        'reference_type',
        'reference_id',
        'movement_date',
        'created_by',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
