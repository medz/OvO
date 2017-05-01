<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_task`;
CREATE TABLE `pw_task` (
  `taskid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `pre_task` int(10) unsigned NULL DEFAULT '0' COMMENT '前置任务ID',
  `is_auto` tinyint(1) NULL DEFAULT '0' COMMENT '是否是自动任务标识',
  `is_display_all` tinyint(1) NULL DEFAULT '0' COMMENT '是否显示给所有用户',
  `view_order` smallint(6) NULL DEFAULT '0' COMMENT '顺序',
  `is_open` tinyint(3) NULL DEFAULT '0' COMMENT '是否开启状态',
  `start_time` int(10) unsigned NULL DEFAULT '0' COMMENT '开始的时间',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '结束时间',
  `period` smallint(6) NULL DEFAULT '0' COMMENT '是否是周期任务',
  `title` varchar(100) NULL DEFAULT '' COMMENT '标题',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  `icon` varchar(200) NULL DEFAULT '' COMMENT '图标路径',
  `user_groups` varchar(255) NULL DEFAULT '-1' COMMENT '可以申请任务的用户组',
  `reward` varchar(255) NULL DEFAULT '' COMMENT '奖励',
  `conditions` varchar(255) NULL DEFAULT '' COMMENT '完成条件',
  PRIMARY KEY (`taskid`),
  KEY `idx_pretask` (`pre_task`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任务';

 */

class PwTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_task', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_task');
    }
}

