<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RActivityImgs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_activity_imgs', function (Blueprint $table) {
            $table->bigIncrements('acimid');
            $table->integer('acid')->foreign('acid')->references('acid')->on('r_activitys');
            $table->string('img', 200);
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
        Schema::dropIfExists('r_activity_imgs');
    }
}
