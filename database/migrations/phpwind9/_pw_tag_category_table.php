<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_tag_category`;
CREATE TABLE `pw_tag_category` (
  `category_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `category_name` char(20) NULL DEFAULT '' COMMENT '分类名称',
  `alias` varchar(15) NULL DEFAULT '' COMMENT '别名',
  `vieworder` smallint(5) unsigned NULL DEFAULT '0' COMMENT '顺序',
  `tag_count` int(10) unsigned NULL DEFAULT '0' COMMENT '话题数',
  `seo_title` varchar(255) NULL DEFAULT '' COMMENT 'seo标题',
  `seo_description` varchar(255) NULL DEFAULT '' COMMENT 'seo描述',
  `seo_keywords` varchar(255) NULL DEFAULT '' COMMENT 'seo关键字',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题分类表';

 */

class PwTagCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_tag_category', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_tag_category');
    }
}

