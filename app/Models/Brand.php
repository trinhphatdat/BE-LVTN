<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $table = 'brands';
    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'status',
    ];
}
