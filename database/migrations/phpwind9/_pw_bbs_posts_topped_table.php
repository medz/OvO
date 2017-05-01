<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_posts_topped`;
CREATE TABLE `pw_bbs_posts_topped` (
  `pid` int(10) unsigned NOT NULL COMMENT '回帖pid',
  `tid` int(10) unsigned NOT NULL COMMENT '帖子tid',
  `floor` int(10) unsigned NULL DEFAULT '0' COMMENT '回帖楼层号',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '贴内置顶操作人',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '贴内置顶时间',
  PRIMARY KEY (`pid`),
  KEY `idx_tid_createdtime` (`tid`,`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='贴内置顶';

 */

class PwBbsPostsToppedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_posts_topped', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_posts_topped');
    }
}

