<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_log`;
CREATE TABLE `pw_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `typeid` tinyint(3) unsigned NULL DEFAULT 0 COMMENT '操作类型ID',
  `created_userid` int(10) unsigned NULL DEFAULT 0 COMMENT '操作者UID',
  `created_time` int(10) unsigned NULL DEFAULT 0 COMMENT '操作时间',
  `created_username` varchar(15) NULL DEFAULT '' COMMENT '操作者名字',
  `operated_uid` int(10) unsigned NULL DEFAULT 0 COMMENT '被操作者UID',
  `operated_username` varchar(15) NULL DEFAULT '' COMMENT '被操作者名字',
  `ip` varchar(40) NULL DEFAULT '' COMMENT '操作IP',
  `fid` smallint(6) unsigned NULL DEFAULT 0 COMMENT '版块ID',
  `tid` int(10) unsigned NULL DEFAULT 0 COMMENT '帖子ID',
  `pid` int(10) unsigned NULL DEFAULT 0 COMMENT '帖子回复ID',
  `extends` varchar(100) NULL DEFAULT '' COMMENT '扩展信息',
  `content` text COMMENT '操作日志内容',
  PRIMARY KEY (`id`),
  KEY `idx_tid_pid` (`tid`, `pid`),
  KEY `idx_fid` (`fid`),
  KEY `idx_created_time` (`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='前台管理日志表';

 */

class PwLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_log', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('主键ID');
            $table->tinyInteger('typeid')->unsigned()->nullable()->default(0)->comment('错误类型');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('操作者UID');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('操作时间');
            $table->string('created_username', 15)->nullable()->default('')->comment('操作者名字');
            $table->integer('operated_uid')->unsigned()->nullable()->default(0)->comment('被操作者UID');
            $table->string('operated_username', 15)->nullable()->default('')->comment('被操作者名字');
            $table->string('ip', 40)->nullable()->default('')->comment('操作IP');
            $table->smallInteger('fid')->unsigned()->nullable()->default(0)->comment('版块ID');
            $table->integer('tid')->unsigned()->nullable()->default(0)->comment('帖子ID');
            $table->integer('pid')->unsigned()->nullable()->default(0)->comment('帖子回复ID');
            $table->string('extends', 100)->nullable()->default('')->comment('扩展信息');
            $table->text('content')->comment('操作日志内容');
            $table->primary('id');
            $table->index(['tid','pid']);
            $table->index('fid');
            $table->index('created_time');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_log');
    }
}

