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
            $table->increments('noid')->unsigned();
            $table->integer('from')->unsigned()->comment('发出者:系统用户rid=0');
            $table->integer('to')->unsigned()->on('r_users')->comment('接收者');
            $table->tinyInteger('type')->unsigned()->comment('类型:1点赞，2评论，0系统通知');
            $table->tinyInteger('read')->unsigned()->default(0)->comment('是否已读：0未读，1已读');
            $table->string('msg', 200)->comment('消息内容');
            $table->timestamps();
            
            
            //外键设置得先定义字段，再设置
            $table->foreign('from')->references('rid')->on('r_users');
            $table->foreign('to')->references('rid')->on('r_users');
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
