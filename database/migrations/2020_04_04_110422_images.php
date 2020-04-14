<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Images extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key', 10)->comment('索引串：moment,activity,activity-c,run,course');
            $table->bigInteger('key_id')->comment('对应表id');
            $table->string('name', 200)->comment('源名称');
            $table->string('store', 200)->comment('存储名称');
            $table->string('extension', 200)->comment('后缀');
            $table->string('mimetype', 200)->comment('图片格式');
            $table->string('size', 200)->comment('源大小');
            $table->integer('width', false, false)->nullable()->comment('宽');
            $table->integer('height', false, false)->nullable()->comment('高');
            $table->integer('mwidth', false, false)->nullable()->comment('压缩宽');
            $table->integer('mheight', false, false)->nullable()->comment('压缩高');
            $table->string('original', 200)->comment('原图url');
            $table->string('thumbnail', 200)->comment('压缩图rul');
            $table->string('error', 50)->nullable()->comment('压缩错误码');
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
        Schema::dropIfExists('images');
    }
}
