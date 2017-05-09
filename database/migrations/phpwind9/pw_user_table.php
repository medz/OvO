<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user`;
CREATE TABLE `pw_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `username` varchar(15) NULL DEFAULT '' COMMENT '用户名字',
  `email` varchar(40) NULL DEFAULT '' COMMENT 'Email地址',
  `password` char(32) NULL DEFAULT '' COMMENT '随机密码',
  `status` smallint(6) unsigned NULL DEFAULT '0' COMMENT '状态',
  `groupid` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '当前用户组ID',
  `memberid` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '会员组ID',
  `regdate` int(10) unsigned NULL DEFAULT '0' COMMENT '注册时间',
  `realname` varchar(50) NULL DEFAULT '' COMMENT '真实姓名',
  `groups` varchar(255) NULL DEFAULT '' COMMENT '用户附加组的ID缓存字段',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户基本表';

 */

class PwUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->comment('用户ID');
            $table->string('username', 15)->nullable()->default('')->comment('用户名字');
            $table->string('email', 40)->nullable()->default('')->comment('Email地址');
            $table->string('password', 32)->nullable()->default('')->comment('随机密码');
            $table->smallinteger('status')->unsigned()->nullable()->default(0)->comment('状态');
            $table->mediuminteger('groupid')->unsigned()->nullable()->default(0)->comment('当前用户组ID');
            $table->mediuminteger('memberid')->unsigned()->nullable()->default(0)->comment('会员组ID');
            $table->integer('regdate')->unsigned()->nullable()->default(0)->comment('注册时间');
            $table->string('realname', 50)->nullable()->default('')->comment('真实姓名');
            $table->string('groups', 255)->nullable()->default('')->comment('用户附加组的ID缓存字段');

            $table->primary('uid');
            $table->primary('username');
            $table->primary('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user');
    }
}

