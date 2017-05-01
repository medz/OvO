<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_hook_inject`;
CREATE TABLE `pw_hook_inject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` char(20) NULL DEFAULT '',
  `app_name` varchar(100) NULL DEFAULT '',
  `hook_name` varchar(100) NULL DEFAULT '' COMMENT '钩子名',
  `alias` varchar(100) NULL DEFAULT '' COMMENT '挂载别名',
  `class` varchar(100) NULL DEFAULT '' COMMENT '挂载类',
  `method` varchar(100) NULL DEFAULT '' COMMENT '调用方法',
  `loadway` varchar(20) NULL DEFAULT '' COMMENT '导入方式',
  `expression` varchar(100) NULL DEFAULT '' COMMENT '条件表达式',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_hook_name` (`hook_name`,`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='钩子挂载方法表';

 */

class PwHookInjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_hook_inject', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_hook_inject');
    }
}

