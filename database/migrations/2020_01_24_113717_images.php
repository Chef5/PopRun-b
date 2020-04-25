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
            $table->increments('id');
            $table->string('key', 10)->comment('索引串：moment,activity,activity-c,course');
            $table->integer('key_id')->comment('对应表id');
            $table->string('name', 200)->comment('源名称');
            $table->string('store', 50)->comment('存储名称');
            $table->string('extension', 10)->comment('后缀');
            $table->string('mimetype', 20)->comment('图片格式');
            $table->integer('size')->comment('源大小');
            $table->integer('width')->comment('宽');
            $table->integer('height')->comment('高');
            $table->integer('mwidth')->default(200)->comment('压缩宽');
            $table->integer('mheight')->comment('压缩高');
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
