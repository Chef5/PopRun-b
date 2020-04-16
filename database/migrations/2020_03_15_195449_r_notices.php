<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_notices', function (Blueprint $table) {
            $table->increments('noid', 10);
            $table->integer('from', false, false)->comment('发出者');
            $table->integer('to', false, false)->comment('接收者');
            $table->tinyInteger('type')->comment('类型:1点赞，2评论，0系统通知');
            $table->tinyInteger('read')->default(0)->comment('是否已读：0未读，1已读');
            $table->string('msg', 200)->nullable()->comment('消息内容');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_notice');
    }
}
