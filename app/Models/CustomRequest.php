<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomRequest extends Model
{
    protected $table = 'custom_requests';
    protected $fillable = [
        'user_id',
        'order_id',
        'model_number',
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
