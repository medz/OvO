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
    public function up()
    {
        Schema::create('pw_tag_category', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->smallIncrements('category_id')->unsigned()->comment('分类id');
            $table->char('category_name', 20)->nullable()->default('')->comment('分类名称');
            $table->string('alias', 15)->nullable()->default('')->comment('别名');
            $table->smallInteger('vieworder')->unsigned()->nullable()->default(0)->comment('顺序');
            $table->integer('tag_count')->unsigned()->nullable()->default(0)->comment('话题数');
            $table->string('seo_title', 255)->nullable()->default('')->comment('seo标题');
            $table->string('seo_description', 255)->nullable()->default('')->comment('seo描述');
            $table->string('seo_keywords', 255)->nullable()->default('')->comment('seo关键字');

            $table->primary('category_id');
        });
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

