<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_medal_info`;
CREATE TABLE `pw_medal_info` (
  `medal_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '勋章ID',
  `name` varchar(50) NULL DEFAULT '' COMMENT '勋章名称',
  `path` varchar(50) NULL DEFAULT '' COMMENT '勋章路径',
  `image` varchar(50) NULL DEFAULT '' COMMENT '勋章图片(系统勋章带路径)',
  `icon` varchar(50) NULL DEFAULT '' COMMENT '勋章图标(系统勋章带路径)',
  `descrip` varchar(255) NULL DEFAULT '' COMMENT '勋章简介',
  `medal_type` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章类型',
  `receive_type` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章获取类型',
  `medal_gids` varchar(50) NULL DEFAULT '' COMMENT '用户组',
  `award_type` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章类型',
  `award_condition` smallint(5) unsigned NULL DEFAULT '0' COMMENT '勋章条件',
  `expired_days` smallint(5) unsigned NULL DEFAULT '0' COMMENT '有效期',
  `isopen` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否开启',
  `vieworder` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`medal_id`),
  KEY `idx_orderid` (`vieworder`),
  KEY `idx_isopen` (`isopen`),
  KEY `idx_award_type` (`award_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='勋章信息表';

 */

class PwMedalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_medal_info', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_medal_info');
    }
}

