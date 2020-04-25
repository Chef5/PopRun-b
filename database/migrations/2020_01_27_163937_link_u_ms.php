<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkUMs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_ms', function (Blueprint $table) {
            $table->increments('linkid')->unsigned();
            $table->integer('rid')->unsigned();
            $table->integer('meid')->unsigned();
            $table->timestamps();

            //外键设置得先定义字段，再设置
            $table->foreign('rid')->references('rid')->on('r_users');
            $table->foreign('meid')->references('meid')->on('r_medals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_u_ms');
    }
}
