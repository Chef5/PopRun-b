<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkUAs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_as', function (Blueprint $table) {
            $table->integer('rid')->unsigned();
            $table->integer('acid')->unsigned();
            $table->tinyInteger('isfinished')->unsigned()->default(0)->comment('0未完成，1已完成');
            $table->timestamps();
            
            //外键设置得先定义字段，再设置
            $table->foreign('rid')->references('rid')->on('r_users');
            $table->foreign('acid')->references('acid')->on('r_activitys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_u_as');
    }
}
