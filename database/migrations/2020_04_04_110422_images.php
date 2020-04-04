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
            $table->string('key', 10);
            $table->bigInteger('key_id');
            $table->string('name', 200);
            $table->string('store', 200);
            $table->string('extension', 200);
            $table->string('mimetype', 200);
            $table->string('size', 200);
            $table->integer('width', false, false)->nullable();
            $table->integer('height', false, false)->nullable();
            $table->integer('mwidth', false, false)->nullable();
            $table->integer('mheight', false, false)->nullable();
            $table->string('original', 200);
            $table->string('thumbnail', 200);
            $table->string('error', 50)->nullable();
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
