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
        }
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

