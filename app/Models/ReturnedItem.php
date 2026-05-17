<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnedItem extends Model
{
    public const RETURN_TYPES = ['customer_return', 'supplier_return'];
    public const CONDITIONS = ['good', 'damaged', 'expired'];
    public const ACTIONS = ['add_back_to_stock', 'move_to_damage', 'move_to_discount', 'return_to_supplier'];

    protected $fillable = [
        'product_variant_id',
        'sale_id',
        'sale_item_id',
        'quantity',
        'return_type',
        'condition',
        'action_type',
        'reason',
        'notes',
        'created_by',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
