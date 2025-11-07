<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = 'order_details';
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'price',
        'quantity',
        'total_price',
    ];
}
