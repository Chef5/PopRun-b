<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RRuns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_runs', function (Blueprint $table) {
            $table->increments('ruid', 20);
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->string('distance', 10)->nullable()->comment('里程');
            $table->string('speed', 10)->nullable()->comment('配速');
            $table->string('location_s', 20)->nullable();
            $table->string('location_w', 20)->nullable();
            $table->timestamp('timestart')->nullable()->comment('开始时间');
            $table->timestamp('timesend')->nullable()->comment('结束时间');
            $table->string('runimg', 200)->nullable()->comment('路径图像');
            $table->string('isshared', 1)->nullable()->comment('是否分享');
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
        Schema::dropIfExists('r_runs');
    }
}
