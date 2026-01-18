<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'stock',
        'defective_stock',
        'original_price',
        'discount',
        'price',
        'image_url',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_variant_id');
    }
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_variant_id');
    }
    public function returnRequestItems()
    {
        return $this->hasMany(ReturnRequestItem::class, 'product_variant_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_variant_id');
    }
}
