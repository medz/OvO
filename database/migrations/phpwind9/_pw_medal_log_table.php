<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_medal_log`;
CREATE TABLE `pw_medal_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '勋章记录ID',
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户ID',
  `medal_id` int(10) unsigned NULL DEFAULT '0' COMMENT '勋章ID',
  `award_status` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章状态：1,进行2，申请3，领取4,显示',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `expired_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  `log_order` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '用户勋章排序',
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `idx_uid_medalid` (`uid`,`medal_id`),
  KEY `idx_expired_time` (`expired_time`),
  KEY `idx_log_order` (`log_order`),
  KEY `idx_awardstatus` (`award_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='勋章记录表';

 */

class PwMedalLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_medal_log', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_medal_log');
    }
}

