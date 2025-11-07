<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $table = 'return_requests';
    protected $fillable = [
        'order_id',
        'user_id',
        'return_type',
        'reason',
        'status',
        'refund_amount',
        'refund_status',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'custom_note',
        'admin_note',
        'admin_id',
        'approved_at',
        'rejected_at',
        'received_at',
        'refunded_at',
    ];
}
