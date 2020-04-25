<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Comments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('coid')->unsigned();
            $table->integer('rid')->unsigned();
            $table->integer('moid')->unsigned();
            $table->string('comment', 200)->comment('评论');
            $table->timestamps();
            
            //外键设置得先定义字段，再设置
            $table->foreign('rid')->references('rid')->on('r_users');
            $table->foreign('moid')->references('moid')->on('r_moments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
