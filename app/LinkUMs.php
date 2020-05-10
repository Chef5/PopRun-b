<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkUMs extends Model
{
    // 可添加字段
    protected $fillable = ['rid', 'meid'];
}
