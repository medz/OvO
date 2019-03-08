<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extras', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_id')->comment('User ID');
            $table->string('name', 50);
            $table->string('value_type', 100);
            $table->json('json_value')->nullable();
            $table->integer('integer_value')->nullable();
            $table->string('string_value')->nullable();

            // indexs.
            $table->unique(['user_id', 'name']);
            $table->index('user_id');
            $table->index('name');
            $table->index('value_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_extras');
    }
}
