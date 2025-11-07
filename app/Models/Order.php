<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'promotion_id',
        'user_id',
        'fullname',
        'email',
        'phone_number',
        'address',
        'text_note',
        'order_status',
        'shipping_status',
        'items_total',
        'shipping_fee',
        'promotion_discount',
        'total_money',
        'payment_method',
        'payment_status',
        'paid_at',
        'is_custom_order',
        'shipped_at',
        'delivered_at',
        'canceled_at',
    ];
}
