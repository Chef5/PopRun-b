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
            $table->string('nickname', 20)->nullable()->comment('昵称');
            $table->string('team', 50)->nullable()->comment('校区');
            $table->tinyInteger('sex')->nullable()->comment('性别');
            $table->string('img', 200)->nullable()->comment('头像');
            $table->string('info', 200)->nullable()->comment('签名');
            $table->string('job', 50)->nullable()->comment('职业');
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
