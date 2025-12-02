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
        'items_total',
        'shipping_fee',
        'promotion_discount',
        'total_money',
        'payment_method',
        'payment_status',
        'vnpay_transaction_id',
        'paid_at',
        'payment_expires_at',
        'shipped_at',
        'delivered_at',
        'canceled_at',

        // GHN fields
        'ghn_order_code',
        'ghn_sort_code',
        'ghn_status',
        'ghn_status_text',
        'ghn_total_fee',
        'ghn_expected_delivery_time',
        'ghn_cod_amount',
        'ghn_last_sync_at',
        'ghn_log',
        'ghn_note',
    ];

    protected $casts = [
        'ghn_log' => 'array',
        'paid_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'canceled_at' => 'datetime',
        'ghn_last_sync_at' => 'datetime',
    ];

    /**
     * Mapping trạng thái GHN sang order_status
     */
    public static function mapGhnStatusToOrderStatus($ghnStatus)
    {
        return match ($ghnStatus) {
            'ready_to_pick', 'picking' => 'confirmed',
            'picked', 'storing', 'transporting' => 'processing',
            'delivering' => 'delivering',
            'delivered' => 'delivered',
            'cancel' => 'cancelled',
            'return', 'returning' => 'returning',
            'returned' => 'returned',
            'delivery_fail' => 'returning',
            'exception' => 'cancelled',
            default => 'pending',
        };
    }

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
