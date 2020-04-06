<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RUsers extends Model
{
    // 可添加字段
    protected $fillable = [
        "rid",
        "openid",
        "nickname",
        "team",
        "sex",
        "img",
        "info",
        "job"
    ];
}
