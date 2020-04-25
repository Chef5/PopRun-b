<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_settings', function (Blueprint $table) {
            $table->integer('rid')->unsigned();
            $table->tinyInteger('job')->unsigned()->default(1)->comment('类型：0不可见，1可见');
            $table->tinyInteger('team')->unsigned()->default(1)->comment('类型：0不可见，1可见');
            $table->tinyInteger('run')->unsigned()->default(1)->comment('类型：0不可见，1可见');
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
        Schema::dropIfExists('r_settings');
    }
}
