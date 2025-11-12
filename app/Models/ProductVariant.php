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
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_variant_id');
    }
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_variant_id');
    }
    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
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
