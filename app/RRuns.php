<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class RRuns extends Model
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

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
