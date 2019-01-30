<?php

use App\Models\Talk;
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
            $table
                ->increments('id');
            $table
                ->integer('publisher_id')
                ->unsigned()
                ->index()
                ->comment('Publisher User ID');
            $table
                ->text('content')
                ->comment('The Talk Content');
            $table
                ->nullableMorphs('repostable'); // repostable_type, repostable_id
            $table
                ->string('resource_type', 50)
                ->nullable()
                ->index()
                ->comment('The talk type');
            $table
                ->json('resource')
                ->nullable()
                ->comment('The talk resource');
            $table
                ->integer('read_count')
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
            $table->timestamps(); // created_at, updated_at
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
