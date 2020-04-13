<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_settings', function (Blueprint $table) {
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->tinyInteger('job')->default(1)->nullable()->comment('类型：0不可见，1可见');
            $table->tinyInteger('team')->default(1)->nullable()->comment('类型：0不可见，1可见');
            $table->tinyInteger('run')->default(1)->nullable()->comment('类型：0不可见，1可见');
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
        Schema::dropIfExists('r_settings');
    }
}
