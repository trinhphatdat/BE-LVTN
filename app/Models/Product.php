<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'brand_id',
        'title',
        'thumbnail',
        'description',
        'gender',
        'material',
        'original_price',
        'discount',
        'price',
        'status',
    ];
    public function cartItem(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
