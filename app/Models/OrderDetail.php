<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'price',
        'quantity',
        'total_price',
    ];
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function returnRequestItems()
    {
        return $this->hasMany(ReturnRequestItem::class, 'order_detail_id');
    }
}
