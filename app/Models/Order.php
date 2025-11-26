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
        'province_id',
        'district_id',
        'ward_id',
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
        'vnpay_transaction_id',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'canceled_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
    public function customRequest()
    {
        return $this->hasOne(CustomRequest::class, 'order_id');
    }
}
