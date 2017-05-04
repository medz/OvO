<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention`;
CREATE TABLE `pw_attention` (
  `uid` int(10) unsigned NOT NULL,
  `touid` int(10) unsigned NOT NULL,
  `created_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`touid`,`uid`),
  KEY `idx_uid_createdtime` (`uid`,`created_time`),
  KEY `idx_touid_createdtime` (`touid`,`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注主表';

 */

class PwAttentionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_attention', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('uid')->unsigned();
            $table->integer('touid')->unsigned();
            $table->integer('created_time')->unsigned()->nullable()->default(0);

            $table->primary('touid', 'uid');
            $table->index('uid', 'created_time');
            $table->index('touid', 'created_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention');
    }
}
