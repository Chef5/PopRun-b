<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_system', function (Blueprint $table) {
            $table->increments('syid', 10);
            $table->integer('to', false, false)->nullable()->comment('接收者');
            $table->integer('type', false, false)->nullable()->comment('类型');
            $table->integer('read', false, false)->nullable()->comment('是否已读');
            $table->string('msg', 20)->nullable()->comment('消息内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_system');
    }
}
