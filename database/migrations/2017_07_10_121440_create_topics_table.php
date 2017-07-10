<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject')->comment('话题标题');
            $table->text('content')->comment('话题内容');
            $table->integer('user_id')->unsigned()->comment('话题创建者');
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
        Schema::dropIfExists('topics');
    }
}
