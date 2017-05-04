<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_space`;
CREATE TABLE `pw_space` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `space_name` varchar(50) NULL DEFAULT '' COMMENT '空间名称',
  `space_descrip` varchar(255) NULL DEFAULT '' COMMENT '空间描述',
  `space_domain` varchar(20) NULL DEFAULT '' COMMENT '二级哉域名',
  `space_style` varchar(20) NULL DEFAULT '' COMMENT '空间风格',
  `back_image` varchar(255) NULL DEFAULT '' COMMENT '背景设置',
  `visit_count` int(10) unsigned NULL DEFAULT '0' COMMENT '访问统计',
  `visitors` TEXT  COMMENT '来访者',
  `tovisitors` TEXT  COMMENT '我的访问记录',
  `space_privacy` tinyint(4) NULL DEFAULT '0' COMMENT '隐私等级',
  PRIMARY KEY (`uid`),
  KEY `idx_space_domain` (`space_domain`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='个人空间信息表';

 */

class PwSpaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_space', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_space');
    }
}

