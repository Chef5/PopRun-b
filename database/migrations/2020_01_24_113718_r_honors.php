<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RHonors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_honors', function (Blueprint $table) {
            $table->increments('hoid')->unsigned();
            $table->string('desc', 4)->unique()->comment('称号描述');
            $table->string('name', 10)->unique()->comment('称号名称');
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
        Schema::dropIfExists('r_honors');
    }
}
