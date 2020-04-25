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
            $table->increments('ruid');
            $table->integer('rid')->unsigned();
            $table->decimal('distance', 5, 2)->nullable()->comment('里程');     //勋章计算：总里程、单次   5表示总位数 2表示小数位
            $table->integer('calorie')->nullable()->comment('卡路里');

            $table->decimal('speed_top', 4, 2)->nullable()->comment('最高配速');  // Syntax error or access violation: 1427 For float(M,D), double(M,D) or decimal(M,D), M must be >= D
            $table->decimal('speed_low', 4, 2)->nullable()->comment('最低配速');
            $table->decimal('speed', 4, 2)->nullable()->comment('平均配速');

            $table->timestamp('time_start')->comment('开始时间'); //勋章计算：早起
            $table->timestamp('time_end')->nullable()->comment('结束时间');
            $table->integer('time_run')->nullable()->comment('运动时间s');   //勋章计算：累计时间、单次

            $table->decimal('latitude_start', 18, 15)->comment('起点维度');
            $table->decimal('longitude_start', 18, 15)->comment('起点经度');
            $table->decimal('latitude_end', 18, 15)->nullable()->comment('终点维度');
            $table->decimal('longitude_end', 18, 15)->nullable()->comment('终点经度');

            $table->tinyInteger('isshared')->default(0)->comment('是否分享');  //是否分享：0未分享，1已分享
            $table->timestamps();
            
            
            //外键设置得先定义字段，再设置
            $table->foreign('rid')->references('rid')->on('r_users');
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
