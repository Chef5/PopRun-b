<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RCources extends Model
{
    // 可添加字段
    protected $fillable = ['title', 'text'];
}
