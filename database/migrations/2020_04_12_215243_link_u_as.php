<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkUAs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_as', function (Blueprint $table) {
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->integer('acid')->foreign('acid')->references('acid')->on('r_activitys');
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
        Schema::dropIfExists('link_u_as');
    }
}
