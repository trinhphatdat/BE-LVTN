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
        'text_custom_couple',
        'order_status',
        'items_total',
        'shipping_fee',
        'shipping_discount',
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

        //  Thêm các trường liên quan đến trả hàng
        'refunded_amount',      // Tổng số tiền đã hoàn trả
        'actual_revenue',       // Doanh thu thực tế sau khi trừ hoàn trả

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

    //  Accessor để tính doanh thu thực tế
    public function getActualRevenueAttribute()
    {
        return $this->total_money - ($this->refunded_amount ?? 0);
    }

    //  Relationship với return requests
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'order_id');
    }

    //  Kiểm tra có return request đang active không
    public function hasActiveReturnRequest()
    {
        return $this->returnRequests()
            ->whereIn('status', ['pending', 'approved', 'received'])
            ->exists();
    }

    //  Lấy tổng số tiền đã/sẽ được hoàn trả
    public function getTotalRefundedAmount()
    {
        return $this->returnRequests()
            ->whereIn('status', ['approved', 'received', 'refunded'])
            ->sum('refund_amount');
    }

    /**
     * Mapping trạng thái GHN sang order_status
     */
    public static function mapGhnStatusToOrderStatus($ghnStatus)
    {
        return match ($ghnStatus) {
            'ready_to_pick' => 'confirmed',
            'picking' => 'processing',
            'cancel' => 'cancelled',
            'money_collect_picking' => 'processing',
            'picked' => 'processing',
            'storing' => 'processing',
            'transporting' => 'delivering',
            'sorting' => 'delivering',
            'delivering' => 'delivering',
            'delivered' => 'delivered',
            'delivery_fail' => 'delivering',
            'waiting_to_return' => 'returning',
            'return' => 'returning',
            'return_transporting' => 'returning',
            'return_sorting' => 'returning',
            'returning' => 'returning',
            'return_fail' => 'returning',
            'returned' => 'returned',
            'exception' => 'processing',
            'damage' => 'cancelled',
            'lost' => 'cancelled',
            default => 'processing',
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
}
