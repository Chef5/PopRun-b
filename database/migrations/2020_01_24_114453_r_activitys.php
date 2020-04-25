<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RActivitys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_activitys', function (Blueprint $table) {
            $table->increments('acid')->unsigned();
            $table->integer('meid')->unsigned()->comment('勋章id');
            $table->integer('cover')->unsigned()->comment('封面图id');
            $table->string('title', 30)->comment('标题');
            $table->string('desc', 200)->nullable()->comment('简要描述:当图片介绍时可为空');
            $table->text('content')->nullable()->comment('活动内容:当图片介绍时可为空');
            $table->timestamp('period')->comment('截止时间');
            $table->timestamps();
            
            //外键设置得先定义字段，再设置
            $table->foreign('meid')->references('meid')->on('r_medals');
            $table->foreign('cover')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_activitys');
    }
}
