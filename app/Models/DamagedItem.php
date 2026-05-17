<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedItem extends Model
{
    public const ACTIONS = ['reduce_from_stock', 'move_to_discount', 'return_to_supplier'];

    protected $fillable = [
        'product_variant_id',
        'quantity',
        'damage_reason',
        'action_type',
        'discounted_product_id',
        'supplier_id',
        'notes',
        'created_by',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function discountedProduct()
    {
        return $this->belongsTo(DiscountedProduct::class);
    }
}
