<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RMoments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_moments', function (Blueprint $table) {
            $table->increments('moid')->unsigned();
            $table->integer('rid')->unsigned();
            $table->text('text')->nullable()->comment('动态内容:图片动态时为空');
            $table->string('location', 50)->nullable()->comment('位置');
            $table->decimal('latitude', 19, 15)->nullable()->comment('位置维度');
            $table->decimal('longitude', 19, 15)->nullable()->comment('位置经度');
            $table->tinyInteger('type')->unsigned()->default(0)->comment('类型：0普通动态，1打卡分享');
            $table->timestamps();
            
            //外键设置得先定义字段，再设置
            $table->foreign('rid')->references('rid')->on('r_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_moments');
    }
}
