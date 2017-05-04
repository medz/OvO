<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_online_user`;
CREATE TABLE `pw_online_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `username` char(15) NULL DEFAULT '' COMMENT '用户名',
  `modify_time` int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `gid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户组',
  `fid` int(10) unsigned NULL DEFAULT '0' COMMENT '版块ID',
  `tid` int(10) unsigned NULL DEFAULT '0' COMMENT '贴子ID',
  `request` char(50) NULL DEFAULT '' COMMENT '当前请求信息',
  PRIMARY KEY (`uid`),
  KEY `idx_fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='在线-用户表';

 */

class PwOnlineUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_online_user', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_online_user');
    }
}

