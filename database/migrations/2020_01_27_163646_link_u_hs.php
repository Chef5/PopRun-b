<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkUHs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_hs', function (Blueprint $table) {
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->integer('hoid')->foreign('hoid')->references('hoid')->on('r_honors');
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
        Schema::dropIfExists('link_u_hs');
    }
}
