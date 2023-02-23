<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Hitokoto extends Model
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
    protected $fillable = ['hitokoto', 'type', 'from', 'creator'];
}
