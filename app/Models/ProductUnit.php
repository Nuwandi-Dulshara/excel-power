<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'unit_name',
        'short_code',
        'description',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_unit_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_unit_id');
    }
}
