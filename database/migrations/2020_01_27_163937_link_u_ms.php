<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkUMs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_u_ms', function (Blueprint $table) {
            $table->increments('linkid', 10);
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->integer('meid')->foreign('meid')->references('meid')->on('r_medals');
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
        Schema::dropIfExists('link_u_ms');
    }
}
