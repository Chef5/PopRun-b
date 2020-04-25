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
            $table->increments('id')->unsigned();
            $table->string('key', 10)->comment('索引串：moment,activity,activity-c,course');
            $table->integer('key_id')->unsigned()->comment('对应表id');
            $table->string('name', 200)->comment('源名称');
            $table->string('store', 50)->comment('存储名称');
            $table->string('extension', 10)->comment('后缀');
            $table->string('mimetype', 20)->comment('图片格式');
            $table->integer('size')->unsigned()->comment('源大小');
            $table->smallInteger('width')->unsigned()->comment('宽');
            $table->smallInteger('height')->unsigned()->comment('高');
            $table->smallInteger('mwidth')->unsigned()->default(200)->comment('压缩宽');
            $table->smallInteger('mheight')->unsigned()->comment('压缩高');
            $table->string('original', 200)->comment('原图url');
            $table->string('thumbnail', 200)->comment('压缩图rul');
            $table->string('error', 50)->nullable()->comment('压缩错误信息');
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
