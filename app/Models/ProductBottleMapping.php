<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBottleMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'min_qty',
        'max_qty',
        'bottle_product_id',
        'is_active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function bottleProduct()
    {
        return $this->belongsTo(Product::class, 'bottle_product_id');
    }
}
