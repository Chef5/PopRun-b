<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RMomentImgs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_moment_imgs', function (Blueprint $table) {
            $table->bigIncrements('moimid');
            $table->bigInteger('moid')->foreign('moid')->references('moid')->on('r_moments');
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
        Schema::dropIfExists('r_moment_imgs');
    }
}
