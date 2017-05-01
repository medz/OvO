<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbsinfo`;
CREATE TABLE `pw_bbsinfo` (
  `id` smallint(3) unsigned NOT NULL auto_increment COMMENT '主键ID',
  `newmember` varchar(15) NULL DEFAULT '' COMMENT '最新会员',
  `totalmember` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '会员总数',
  `higholnum` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '最高在线人数',
  `higholtime` int(10) unsigned NULL DEFAULT '0' COMMENT '最高在线发生日期',
  `yposts` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '昨日发帖数',
  `hposts` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '最高日发帖数',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='论坛信息表';

 */

class PwBbsinfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbsinfo', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbsinfo');
    }
}

