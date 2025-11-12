<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequestItem extends Model
{
    protected $table = 'return_request_items';
    protected $fillable = [
        'return_request_id',
        'order_detail_id',
        'product_variant_id',
        'ordered_quantity',
        'return_quantity',
        'price',
        'refund_amount',
    ];
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
