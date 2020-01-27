<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkULikeMs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_like_ms', function (Blueprint $table) {
            $table->bigInteger('moid')->foreign('moid')->references('moid')->on('r_moments');
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
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
        Schema::dropIfExists('link_u_like_ms');
    }
}
