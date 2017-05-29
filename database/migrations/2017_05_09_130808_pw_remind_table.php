<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_remind`;
CREATE TABLE `pw_remind` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `touid` varchar(255) NULL DEFAULT '' COMMENT '最近提醒人',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='@最近提醒表';

 */

class PwRemindTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_remind', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户uid');
            $table->string('touid', 255)->nullable()->default('')->comment('最近提醒人');

            $table->primary('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_remind');
    }
}
