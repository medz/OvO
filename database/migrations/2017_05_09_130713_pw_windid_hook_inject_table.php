<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_hook_inject`;
CREATE TABLE `pw_windid_hook_inject` (
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

class PwWindidHookInjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_hook_inject', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned();
            $table->char('app_id', 20)->nullable()->default('');
            $table->string('app_name', 100)->nullable()->default('');
            $table->string('hook_name', 100)->nullable()->default('')->comment('钩子名');
            $table->string('alias', 100)->nullable()->default('')->comment('挂载别名');
            $table->string('class', 100)->nullable()->default('')->comment('挂载类');
            $table->string('method', 100)->nullable()->default('')->comment('调用方法');
            $table->string('loadway', 20)->nullable()->default('')->comment('钩子名');
            $table->string('expression', 100)->nullable()->default('')->comment('钩子名');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('修改时间');
            $table->string('description', 255)->nullable()->default('')->comment('描述');

            $table->primary('id');
            $table->index(['hook_name', 'alias']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_hook_inject');
    }
}
