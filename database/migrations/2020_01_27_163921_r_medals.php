<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RMedals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_medals', function (Blueprint $table) {
            $table->increments('meid', 4);
            $table->string('mkey', 20)->nullable()->unique()->comment('检索串');
            $table->tinyInteger('type')->default(0)->comment('类型：1可重复获得，0不可重复');
            $table->string('desc', 100)->nullable()->comment('勋章简介');
            $table->string('name', 20)->nullable()->unique()->comment('勋章名称');
            $table->string('img', 200)->nullable()->unique()->comment('勋章图标');
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
        Schema::dropIfExists('r_medals');
    }
}
