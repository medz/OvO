<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum_nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('Node name.');
            $table->string('description')->nullable()->comment('Node description.');
            $table->string('icon')->nullable()->comment('Node icon path.');
            $table->string('color')->nullable()->comment('Node color.');
            $table->unsignedInteger('threads_count')->nullable()->default(0);
            $table->unsignedInteger('followers_count')->nullable()->default(0);
            $table->timestamps();

            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_nodes');
    }
}
