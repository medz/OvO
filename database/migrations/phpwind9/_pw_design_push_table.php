<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_push`;
CREATE TABLE `pw_design_push` (
  `push_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推送ID',
  `push_from_id` int(10) unsigned NULL DEFAULT '0' COMMENT '来源ID',
  `push_from_model` varchar(20) NULL DEFAULT '' COMMENT '来源应用',
  `module_id` int(10) unsigned NULL DEFAULT '0' COMMENT '所属模块ID',
  `push_standard` varchar(255) NULL DEFAULT '' COMMENT '标准化标签',
  `push_style` varchar(255) NULL DEFAULT '' COMMENT '样式',
  `push_orderid` int(10) unsigned NULL DEFAULT '0' COMMENT '排序',
  `push_extend` text COMMENT '推送内容',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '推送人ID',
  `author_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '作者UID',
  `status` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '状态显示：0 需要审核：1',
  `neednotice` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否需要发送站内信',
  `check_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '审核人ID',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '推送时间',
  `start_time` int(10) unsigned NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  `checked_time` int(10) unsigned NULL DEFAULT '0' COMMENT '审核时间',
  PRIMARY KEY (`push_id`),
  KEY `idx_end_time` (`end_time`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='信息推送表';

 */

class PwDesignPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_push', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_design_push');
    }
}

