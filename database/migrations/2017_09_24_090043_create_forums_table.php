<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('论坛名称');
            $table->string('avatar')->nullable()->default(null)->comment('论坛 logo');
            $table->string('bg_color', 50)->nullable()->default(null)->comment('背景颜色');
            $table->integer('topic_count')->unsigned()->nullable()->default(0)->comment('论坛下话题统计');
            $table->integer('post_count')->unsigned()->nullable()->default(0)->comment('论题下讨论统计');
            $table->tinyInteger('allow_not_category')->unsigned()->nullable()->default(1)->comment('是否允许不选择类别');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forums');
    }
}
