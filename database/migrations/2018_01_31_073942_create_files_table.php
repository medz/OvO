<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner')->unsigned()->comment('File owner.');
            $table->string('filename')->comment('File name.');
            $table->string('origin_name')->comment('File origin name.');
            $table->tinyInteger('state')->nullable()->default(0)->comment('0: WAIT, 1: SUCCESS');
            $table->timestamps();

            $table->index('filename');
            $table->index('owner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
