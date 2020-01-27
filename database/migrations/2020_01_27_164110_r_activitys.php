<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RActivitys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_activitys', function (Blueprint $table) {
            $table->increments('acid', 10);
            $table->integer('rid')->foreign('rid')->references('rid')->on('r_users');
            $table->string('title', 50)->nullable();
            $table->string('desc', 200)->nullable()->comment('简要描述');
            $table->string('cover', 200)->nullable()->comment('封面图');
            $table->longText('content')->nullable()->comment('活动内容');
            $table->string('period', 10)->nullable()->comment('持续时长');
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
        Schema::dropIfExists('r_activitys');
    }
}
