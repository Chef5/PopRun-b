<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RActivitys extends Model
{
    // 可添加字段
    protected $fillable = ['rid', 'title', 'desc', 'cover', 'content', 'period'];
}
