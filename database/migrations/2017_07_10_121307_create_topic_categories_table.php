<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('类别名称');
            $table->integer('icon')->nullable()->default(null)->comment('icon');
            $table->string('bg_color', 50)->nullable()->default(null)->comment('背景颜色');
            $table->integer('topic_count')->unsigned()->nullable()->default(0)->comment('话题统计');
            $table->integer('post_count')->unsigned()->nullable()->default(0)->comment('讨论统计');
            $table->integer('parent')->unsigned()->nullable()->default(null)->comment('父级类别');
            $table->tinyInteger('new')->unsigned()->nullable()->default(1)->comment('是否允许发布话题');
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
        Schema::dropIfExists('topic_categories');
    }
}
