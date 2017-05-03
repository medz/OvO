<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAdvertisement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*

            // 原始 sql：
            DROP TABLE IF EXISTS `pw_advertisement`;
            CREATE TABLE `pw_advertisement` (
              `pid` int(10) unsigned NOT NULL,
              `identifier` varchar(30) NOT NULL,
              `type_id` tinyint(3) unsigned NOT NULL,
              `width` smallint(6) NULL DEFAULT '0',
              `height` smallint(6) NULL DEFAULT '0',
              `status` tinyint(3) NULL DEFAULT '0',
              `schedule` varchar(100) NOT NULL,
              `show_type` tinyint(3) NULL DEFAULT '0',
              `condition` text,
              UNIQUE KEY `pid` (`pid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='广告位数据表';

         */

        Schema::create('pw_advertisement', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('pid')->unsigned();
            $table->string('identifier', 30);
            $table->tinyInteger('type_id')->unsigned();
            $table->smallInteger('width')->nullable()->default(0);
            $table->smallInteger('height')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->string('schedule', 100);
            $table->tinyInteger('show_type')->nullable()->default(0);
            $table->text('condition');

            $table->unique('pid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_advertisement');
    }
}
