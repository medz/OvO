<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_page`;
CREATE TABLE `pw_design_page` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '页面ID',
  `page_type` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '页面类型',
  `page_name` varchar(50) NULL DEFAULT '' COMMENT '页面名称',
  `page_router` varchar(50) NULL DEFAULT '' COMMENT '页面路由信息',
  `page_unique` int(10) unsigned NULL DEFAULT '0' COMMENT '页面唯一标识',
  `is_unique` int(10) unsigned NULL DEFAULT '0' COMMENT '唯一标识的标识',
  `module_ids` TEXT  COMMENT '页面模块',
  `struct_names` TEXT  COMMENT '页面结构',
  `segments` TEXT COMMENT '页面模块片段',
  `design_lock` varchar(50) NULL DEFAULT '' COMMENT '编辑加锁',
  PRIMARY KEY (`page_id`),
  KEY `idx_pagerouter` (`page_router`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='页面信息表';

 */

class PwDesignPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_page', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('page_id')->unsigned()->comment('页面ID');
            $table->tinyInteger('page_type')->unsigned()->nullable()->default(0)->comment('页面类型');
            $table->string('page_name', 50)->nullable()->default('')->comment('页面名称');
            $table->string('page_router', 50)->nullable()->default('')->comment('页面路由信息');
            $table->integer('page_unique')->unsigned()->nullable()->default(0)->comment('页面唯一标识');
            $table->integer('is_unique')->unsigned()->nullable()->default(0)->comment('唯一标识的标识');
            $table->text('module_ids')->comment('页面模块');
            $table->text('struct_names')->comment('页面结构');
            $table->text('segments')->comment('页面模块片段');
            $table->string('design_lock', 50)->nullable()->default('')->comment('编辑加锁');

            $table->index('page_router');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_page');
    }
}
