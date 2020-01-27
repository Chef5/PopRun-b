<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RMoments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_moments', function (Blueprint $table) {
            $table->bigIncrements('moid');
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->string('text', 500)->nullable()->comment('动态内容');
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
        Schema::dropIfExists('r_moments');
    }
}
