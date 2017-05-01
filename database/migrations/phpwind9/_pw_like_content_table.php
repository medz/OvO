<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_like_content`;
CREATE TABLE `pw_like_content` (
  `likeid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '喜欢ID',
  `typeid` int(10) unsigned NULL DEFAULT '0' COMMENT '喜欢来源类型',
  `fromid` int(10) unsigned NULL DEFAULT '0' COMMENT '来源ID',
  `isspecial` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否特殊设置',
  `users` varchar(255) NULL DEFAULT '' COMMENT '喜欢的用户ID',
  `reply_pid` int(10) unsigned NULL DEFAULT '0' COMMENT '最新回复ID',
  PRIMARY KEY (`likeid`),
  KEY `idx_isspecial` (`isspecial`),
  KEY `idx_typeid_fromid` (`typeid`,`fromid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='喜欢内容表';

 */

class PwLikeContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_like_content', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_like_content');
    }
}

