<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_like_source`;
CREATE TABLE `pw_like_source` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `subject` varchar(250) NULL DEFAULT '' COMMENT '标题',
  `sourceUrl` varchar(50) NULL DEFAULT '' COMMENT '来源URL',
  `fromApp` varchar(20) NULL DEFAULT '' COMMENT '来源应用名称',
  `fromid` int(10) unsigned NULL DEFAULT '0' COMMENT '来源ID',
  `like_count` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '喜欢数统计',
  PRIMARY KEY (`sid`),
  KEY `idx_fromid` (`fromid`,`fromApp`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='喜欢app来源表';

 */

class PwLikeSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_like_source', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_like_source');
    }
}

