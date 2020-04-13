<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RCourses extends Model
{
    // 可添加字段
    protected $fillable = ['title', 'text'];
}
