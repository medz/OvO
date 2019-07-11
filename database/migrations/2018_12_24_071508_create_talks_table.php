<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTalksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('talks', function (Blueprint $table) {
            $table->increments('id')->comment('Talk ID');
            $table->increments('publisher_id')->comment('Publisher User ID');
            $table->text('content')->comment('The Talk Content');
            $table->morphs('shareable'); // shareable_type, shareable_id
            $table->uuid('last_comment_id')->nullable()->comment('The Talk last comment');
            $table->json('media')->nullable()->comment('The Talk media');

            $table
                ->integer('views_count')
                ->nullable()
                ->default(0);
            $table
                ->integer('likes_count')
                ->nullable()
                ->default(0);
            $table
                ->integer('comments_count')
                ->nullable()
                ->default(0);
            $table
                ->integer('shares_count')
                ->nullable()
                ->default(0);

            $table->timestamps(); // created_at, updated_at

            $table->primary('id');
            $table->index('publisher_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('talks');
    }
}
