<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_users', function (Blueprint $table) {
            $table->increments('rid', 10);
            $table->string('openid', 50)->unique();
            $table->string('nickname', 20)->nullable();
            $table->string('sex', 1)->nullable();
            $table->string('img', 200)->nullable();
            $table->string('info', 200)->nullable();
            $table->string('job', 50)->nullable();
            $table->timestamps();
            // $table->primary(['rid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_users');
    }
}
