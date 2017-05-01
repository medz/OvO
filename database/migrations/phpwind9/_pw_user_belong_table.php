<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_belong`;
CREATE TABLE `pw_user_belong` (
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户ID',
  `gid` mediumint(8) NULL DEFAULT '0' COMMENT '用户组ID',
  `endtime` int(10) unsigned NULL DEFAULT '0' COMMENT '有效期',
  UNIQUE KEY `idx_uid_gid` (`uid`,`gid`),
  KEY `idx_gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户所属用户组表';

 */

class PwUserBelongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_user_belong', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_belong');
    }
}

