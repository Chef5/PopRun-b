<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RRuns extends Model
{
    // 可添加字段
    protected $fillable = [
        'rid', 
        'distance', 
        'calorie', 
        'speed_top', 
        'speed_low',
        'speed',
        'time_start',
        'time_end',
        'time_run',
        'latitude_start',
        'longitude_start',
        'latitude_end',
        'longitude_end',
        'isshared'
    ];
}
