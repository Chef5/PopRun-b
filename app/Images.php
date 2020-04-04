<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    // 可添加字段
    protected $fillable = ['key', 'key_id', 'name', 'store', 'extension', 'mimetype', 'size', 'width', 'height', 'mwidth', 'mheight', 'original', 'thumbnail', 'error', 'created_at', 'updated_at'];
}
