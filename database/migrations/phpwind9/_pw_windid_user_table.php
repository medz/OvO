<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_user`;
CREATE TABLE `pw_windid_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(15) NULL DEFAULT '' COMMENT '用户名字',
  `email` varchar(80) NULL DEFAULT '' COMMENT 'Email',
  `password` char(32) NULL DEFAULT '' COMMENT '密码',
  `salt` char(6) NULL DEFAULT '' COMMENT '盐值',
  `safecv` char(8) NULL DEFAULT '' COMMENT '安全问题',
  `regdate` int(10) unsigned NULL DEFAULT '0' COMMENT '注册时间',
  `regip` varchar(20) NULL DEFAULT '' COMMENT '注册IP',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='windid用户基本信息表';

 */

class PwWindidUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_windid_user', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_windid_user');
    }
}

