<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumTopicPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_topic_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('forum_topic_id')->unsigned()->comment('所属话题');
            $table->integer('user_id')->unsigned()->comment('讨论发布者');
            $table->text('body')->comment('讨论内容');
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
        Schema::dropIfExists('forum_topic_posts');
    }
}
