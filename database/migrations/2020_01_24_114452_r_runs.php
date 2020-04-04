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
            $table->string('distance', 10)->nullable()->comment('里程');     //勋章计算：总里程、单次
            $table->string('calorie', 10)->nullable()->comment('卡路里');

            $table->string('speed_top', 10)->nullable()->comment('最高配速');
            $table->string('speed_low', 10)->nullable()->comment('最低配速');
            $table->string('speed', 10)->nullable()->comment('平均配速');

            $table->timestamp('time_start')->nullable()->comment('开始时间'); //勋章计算：早起
            $table->timestamp('time_end')->nullable()->comment('结束时间');
            $table->integer('time_run', false, false)->nullable()->comment('运动时间s');   //勋章计算：累计时间、单次

            $table->string('latitude_start', 20)->nullable()->comment('起点维度');
            $table->string('longitude_start', 20)->nullable()->comment('起点经度');
            $table->string('latitude_end', 20)->nullable()->comment('终点维度');
            $table->string('longitude_end', 20)->nullable()->comment('终点经度');

            $table->string('isshared', 1)->default('0')->comment('是否分享');  //是否分享：0未分享，1已分享
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
