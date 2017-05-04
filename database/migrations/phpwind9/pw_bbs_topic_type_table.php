<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_topic_type`;
CREATE TABLE `pw_bbs_topic_type` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主题分类ID',
`fid` int(10) unsigned NULL DEFAULT '0' COMMENT '版块ID',
`name` varchar(255) NOT NULL COMMENT '主题分类名称',
`parentid` int(10) unsigned NULL DEFAULT '0' COMMENT '上级主题分类ID',
`logo` varchar(255) NULL DEFAULT '' COMMENT '主题分类图标',
`vieworder` tinyint(3) NULL DEFAULT '0' COMMENT '显示排序',
`issys` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否管理专用(1-是,0-否)',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='主题分类表';

 */

class PwBbsTopicTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_topic_type', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('主题分类ID');
            $table->integer('fid')->unsigned()->nullable()->default(0)->comment('版块ID');
            $table->string('name', 255)->comment('主题分类名称');
            $table->integer('parentid')->unsigned()->nullable()->default(0)->comment('上级主题分类ID');
            $table->string('logo', 255)->nullable()->default('')->comment('主题分类图标');
            $table->tinyInteger('vieworder')->unsigned()->nullable()->default(0)->comment('显示排序');
            $table->tinyInteger('issys')->unsigned()->nullable()->default(0)->comment('是否管理专用(1-是,0-否)');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_topic_type');
    }
}
