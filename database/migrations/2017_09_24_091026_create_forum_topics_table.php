<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forum_id')->unsigned()->comment('所属论坛');
            $table->integer('forum_topic_categories_id')->unsigned()->nullable()->default(null)->comment('论坛下分类');
            $table->integer('user_id')->unsigned()->comment('话题创建者');
            $table->string('subject')->comment('话题标题');
            $table->text('body')->comment('话题内容');
            $table->integer('view_count')->unsigned()->nullable()->default(0)->comment('查看数');
            $table->integer('post_count')->unsigned()->nullable()->default(0)->comment('讨论数');
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
        Schema::dropIfExists('forum_topics');
    }
}
