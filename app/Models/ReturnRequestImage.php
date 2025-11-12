<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequestImage extends Model
{
    protected $table = 'return_request_images';
    protected $fillable = [
        'return_request_id',
        'image_url',
        'description',
    ];
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }
}
