<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileWithsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_withs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id')->unsigned()->comment('文件ID');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->morphs('channel');
            $table->string('size', 50)->nullable()->default(null)->comment('图像尺寸，WxH');
            $table->timestamps();

            $table->index('file_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_withs');
    }
}
