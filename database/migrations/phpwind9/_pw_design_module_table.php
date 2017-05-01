<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_module`;
CREATE TABLE `pw_design_module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模块ID',
  `page_id` int(10) unsigned NULL DEFAULT '0' COMMENT '所属页面ID',
  `segment` varchar(50) NULL DEFAULT '' COMMENT '模块所属片段',
  `module_struct` varchar(20) NULL DEFAULT '' COMMENT '模块结构',
  `model_flag` varchar(20) NULL DEFAULT '' COMMENT '所属模块分类',
  `module_name` varchar(50) NULL DEFAULT '' COMMENT '模块名称',
  `module_property` text COMMENT '模块属性',
  `module_title` text COMMENT '模块标题',
  `module_style` text COMMENT '模块样式',
  `module_compid` int(10) unsigned NULL DEFAULT '0' COMMENT '模版元件ID',
  `module_tpl` text COMMENT '模块模版代码',
  `module_cache` varchar(255) NULL DEFAULT '' COMMENT '模块更新设置',
  `isused` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否使用',
  `module_type` tinyint(1) unsigned NULL DEFAULT '1' COMMENT '模块类型 1 拖曳2 导入3 后台添加',
  PRIMARY KEY (`module_id`),
  KEY `idx_pageid` (`page_id`),
  KEY `idx_moduletype` (`module_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='调用模块表';

 */

class PwDesignModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_design_module', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_design_module');
    }
}

