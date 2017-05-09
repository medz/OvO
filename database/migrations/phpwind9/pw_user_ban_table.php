<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_ban`;
CREATE TABLE `pw_user_ban` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户ID',
  `typeid` char(20) NULL DEFAULT '' COMMENT '类型',
  `fid` int(10) unsigned NULL DEFAULT '0' COMMENT '版块ID---未用',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '结束时间',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '执行者ID',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '开始时间',
  `reason` varchar(80) NULL DEFAULT '' COMMENT '操作原因',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uid_typeid_fid` (`uid`,`typeid`,`fid`),
  KEY `idx_createdUid` (`created_userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户禁止记录表';

 */

class PwUserBanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_ban', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('id')->unsigned();
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户ID');
            $table->char('typeid', 20)->nullable()->default('')->comment('类型');
            $table->integer('fid')->unsigned()->nullable()->default(0)->comment('版块ID---未用');
            $table->integer('end_time')->unsigned()->nullable()->default(0)->comment('结束时间');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('执行者ID');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('开始时间');
            $table->string('reason', 80)->nullable()->default('')->comment('操作原因');

            $table->primary('id');
            $table->unique(['uid', 'typeid', 'fid']);
            $table->index('created_userid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_ban');
    }
}
