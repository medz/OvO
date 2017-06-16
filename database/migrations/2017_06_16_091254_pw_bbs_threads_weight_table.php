<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwBbsThreadsWeightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_bbs_threads_weight', function (Blueprint $table) {
            $table->integer('tid')->unsigned()->comment('帖子ID');
            $table->integer('weight')->nullable()->default(0);
            $table->integer('create_time')->unsigned()->nullable()->default(0);
            $table->integer('create_userid')->unsigned()->nullable()->default(0);
            $table->string('create_username', 150)->nullable()->default(null);
            $table->tinyInteger('isenable')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_threads_weight');
    }
}
