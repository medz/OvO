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
    public function run()
    {
        Schema::create('pw_user', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
        }
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

