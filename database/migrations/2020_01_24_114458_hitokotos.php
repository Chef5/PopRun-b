<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Hitokotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hitokotos', function (Blueprint $table) {
            $table->increments('hiid');
            $table->string('hitokoto', 200)->comment('一言');
            $table->string('type', 1)->comment('类型');
            $table->string('from', 50)->nullable()->comment('来源');
            $table->string('creator', 30)->nullable()->comment('作者');
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
        Schema::table('hitokoto', function (Blueprint $table) {
            //
        });
    }
}
