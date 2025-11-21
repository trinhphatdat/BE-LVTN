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
        'min_price',
        'max_price',
        'has_discount',
        'max_discount',
        'status',
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }
}
