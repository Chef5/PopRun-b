<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hitokoto extends Model
{
    // 可添加字段
    protected $fillable = ['hitokoto', 'type', 'from', 'creator'];
}
