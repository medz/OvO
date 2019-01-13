<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJurisdictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurisdictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('User ID');
            $table->string('node')->comment('Jurisdiction Node');
            $table->timestamps();

            $table->index('user_id');
            $table->index('node');
            $table->unique(['user_id', 'node']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurisdictions');
    }
}
