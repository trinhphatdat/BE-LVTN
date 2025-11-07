<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';
    protected $fillable = [
        'user_id',
        'product_variant_id',
        'rating',
        'comment',
        'status',
        'approved_at',
    ];
}
