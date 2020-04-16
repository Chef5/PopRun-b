<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RNotices extends Model
{
    // 可添加字段
    protected $fillable = ['from', 'to', 'type', 'msg'];
}
