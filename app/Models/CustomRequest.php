<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomRequest extends Model
{
    protected $table = 'custom_requests';
    protected $fillable = [
        'user_id',
        'order_id',
        'logo_number',
        'size',
        'quantity',
        'custom_text',
        'price',
        'custom_fee',
        'total_price',
        'status',
        'admin_note',
        'admin_id',
        'reject_reason',
        'approved_at',
        'rejected_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
