<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id')->unsigned()->comment('话题ID');
            $table->integer('user_id')->unsigned()->comment('讨论发布者');
            $table->text('content')->comment('讨论内容');
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
        Schema::dropIfExists('topic_posts');
    }
}
