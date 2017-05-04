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
    public function up()
    {
        Schema::create('pw_design_module', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('module_id')->unsigned()->comment('模块ID');
            $table->integer('page_id')->unsigned()->nullable()->default(0)->comment('所属页面ID');
            $table->string('segment', 50)->nullable()->default('')->comment('模块所属片段');
            $table->string('module_struct', 20)->nullable()->default('')->comment('模块结构');
            $table->string('model_flag', 20)->nullable()->default('')->comment('所属模块分类');
            $table->string('module_name', 50)->nullable()->default('')->comment('模块名称');
            $table->text('module_property')->comment('模块属性');
            $table->text('module_title')->comment('模块标题');
            $table->text('module_style')->comment('模块样式');
            $table->integer('module_compid')->unsigned()->nullable()->default(0)->comment('模版元件ID');
            $table->text('module_tpl')->comment('模块模版代码');
            $table->string('module_cache', 255)->nullable()->default('')->comment('模块更新设置');
            $table->tinyInteger('isused')->unsigned()->nullable()->default(0)->comment('是否使用');
            $table->tinyInteger('module_type')->unsigned()->nullable()->default(1)->comment('模块类型 1 拖曳2 导入3 后台添加');
            $table->primary('module_id');
            $table->index('page_id');
            $table->index('module_type');
        });
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
