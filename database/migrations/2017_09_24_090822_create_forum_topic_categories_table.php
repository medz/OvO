<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTopicCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_topic_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forum_id')->unsigned()->comment('所属论坛');
            $table->string('name', 150)->comment('类别名称');
            $table->integer('topic_count')->unsigned()->nullable()->default(0)->comment('话题统计');
            $table->integer('post_count')->unsigned()->nullable()->default(0)->comment('讨论统计');
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
        Schema::dropIfExists('forum_topic_categories');
    }
}
