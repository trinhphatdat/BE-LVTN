<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
        'price',
        'total_price',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Kiểm tra xem item có phải là couple không
    public function isCouple()
    {
        return $this->productVariant?->product?->product_type === 'couple';
    }
}
