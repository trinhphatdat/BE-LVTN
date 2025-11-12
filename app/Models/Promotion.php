<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';
    protected $fillable = [
        'name',
        'url_image',
        'description',
        'discount_type',
        'discount_value',
        'min_order_value',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];
    public function orders()
    {
        return $this->hasMany(Order::class, 'promotion_id');
    }
}
