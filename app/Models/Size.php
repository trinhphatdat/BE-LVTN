<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'sizes';
    protected $fillable = [
        'name',
        'length',
        'width',
        'sleeve',
        'status',
    ];
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
