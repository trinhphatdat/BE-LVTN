<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colors';
    protected $fillable = [
        'name',
        'hex_code',
        'status',
    ];
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'color_id');
    }
}
