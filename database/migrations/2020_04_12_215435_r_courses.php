<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RCourses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_courses', function (Blueprint $table) {
            $table->bigIncrements('rcid');
            $table->string('title')->nullable()->comment('教程标题');
            $table->longText('text')->nullable()->comment('教程内容');
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
        Schema::dropIfExists('r_courses');
    }
}
