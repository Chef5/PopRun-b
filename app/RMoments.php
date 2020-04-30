<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RMoments extends Model
{
    // 可添加字段
    protected $fillable = [
        'rid',
        'text', 
        'location', 
        'latitude', 
        'longitude', 
        'type'
    ];
}
