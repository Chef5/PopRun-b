<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkULikeMs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_like_ms', function (Blueprint $table) {
            $table->integer('moid')->unsigned();
            $table->integer('rid')->unsigned();
            $table->timestamps();
            
            //外键设置得先定义字段，再设置
            $table->foreign('moid')->references('moid')->on('r_moments');
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
        Schema::dropIfExists('link_u_like_ms');
    }
}
