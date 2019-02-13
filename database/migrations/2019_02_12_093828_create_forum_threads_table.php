<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('publisher_id')->index();
            $table->unsignedInteger('node_id')->index();
            $table->string('title');
            $table->unsignedInteger('last_comment_id')->nullable();
            $table->unsignedInteger('views_count')->nullable()->default(0);
            $table->unsignedInteger('likes_count')->nullable()->default(0);
            $table->unsignedInteger('comments_count')->nullable()->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('excellent_at')->nullable()->index();
            $table->timestamp('pinned_at')->nullable()->index();
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
        Schema::dropIfExists('forum_threads');
    }
}
