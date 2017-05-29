<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_tag`;
CREATE TABLE `pw_tag` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '话题id',
  `parent_tag_id` int(10) unsigned NULL DEFAULT '0' COMMENT '上级话题id',
  `tag_name` char(60) NULL DEFAULT '' COMMENT '话题名称',
  `tag_logo` varchar(255) NULL DEFAULT '' COMMENT '话题logo',
  `ifhot` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '允许热门',
  `excerpt` varchar(255) NULL DEFAULT '' COMMENT '摘要',
  `content_count` int(10) unsigned NULL DEFAULT '0' COMMENT '内容数',
  `attention_count` int(10) unsigned NULL DEFAULT '0' COMMENT '关注数',
  `hits` int(10) unsigned NULL DEFAULT '0' COMMENT '点击数',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '创建人',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `iflogo` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否有logo',
  `seo_title` varchar(255) NULL DEFAULT '' COMMENT 'seo标题',
  `seo_description` varchar(255) NULL DEFAULT '' COMMENT 'seo描述',
  `seo_keywords` varchar(255) NULL DEFAULT '' COMMENT 'seo关键字',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `idx_tagname` (`tag_name`),
  KEY `idx_parenttagid` (`parent_tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题表';

 */

class PwTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_tag', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('tag_id')->unsigned()->comment('话题id');
            $table->integer('parent_tag_id')->unsigned()->nullable()->default(0)->comment('举报应用id');
            $table->char('tag_name', 60)->nullable()->default('')->comment('上级话题id');
            $table->string('tag_logo', 255)->nullable()->default('')->comment('话题logo');
            $table->tinyInteger('ifhot')->unsigned()->nullable()->default(1)->comment('允许热门');
            $table->string('excerpt', 255)->nullable()->default('')->comment('摘要');
            $table->integer('content_count')->unsigned()->nullable()->default(0)->comment('内容数');
            $table->integer('attention_count')->unsigned()->nullable()->default(0)->comment('关注数');
            $table->integer('hits')->unsigned()->nullable()->default(0)->comment('点击数');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('创建人');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->tinyInteger('iflogo')->unsigned()->nullable()->default(0)->comment('是否有logo');
            $table->string('seo_title', 255)->nullable()->default('')->comment('seo标题');
            $table->string('seo_description', 255)->nullable()->default('')->comment('seo描述');
            $table->string('seo_keywords', 255)->nullable()->default('')->comment('seo关键字');

            $table->index('tag_name');
            $table->index('parent_tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_tag');
    }
}
