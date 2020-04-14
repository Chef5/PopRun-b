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
            $table->integer('meid')->foreign('meid')->references('meid')->on('r_medals');
            $table->string('title', 50)->nullable()->comment('标题');
            $table->tinyInteger('cover')->default(0)->comment('封面图id');
            $table->string('desc', 200)->nullable()->comment('简要描述');
            $table->longText('content')->nullable()->comment('活动内容');
            $table->timestamp('period')->nullable()->comment('截止时间');
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
