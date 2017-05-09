<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_education`;
CREATE TABLE `pw_user_education` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户ID',
  `schoolid` int(10) unsigned NULL DEFAULT '0' COMMENT '学校ID',
  `degree` tinyint(2) unsigned NULL DEFAULT '0' COMMENT '学历ID',
  `start_time` smallint(5) unsigned NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid_startTime` (`uid`,`start_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户教育信息表';

 */

class PwUserEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_education', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned();
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户ID');
            $table->integer('schoolid')->unsigned()->nullable()->default(0)->comment('学校ID');
            $table->tinyInteger('degree')->unsigned()->nullable()->default(0)->comment('学历ID');
            $table->smallinteger('start_time')->unsigned()->nullable()->default(0)->comment('时间');

            $table->primary('id');
            $table->index(['uid', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_education');
    }
}
