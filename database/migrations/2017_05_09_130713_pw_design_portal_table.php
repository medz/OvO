<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_portal`;
CREATE TABLE `pw_design_portal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `pagename` varchar(50) NULL DEFAULT '' COMMENT '页面名称',
  `title` varchar(255) NULL DEFAULT '' COMMENT 'title信息',
  `keywords` varchar(255) NULL DEFAULT '' COMMENT 'keywords信息',
  `description` varchar(255) NULL DEFAULT '' COMMENT 'description信息',
  `domain` varchar(50) NULL DEFAULT '' COMMENT '二级域名',
  `cover` varchar(255) NULL DEFAULT '' COMMENT '封面图片',
  `isopen` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否使用',
  `header` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否使用公共头',
  `navigate` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否使用公共导航',
  `footer` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否使用公共页脚',
  `template` varchar(50) NULL DEFAULT '' COMMENT '所使用的模版名',
  `style` varchar(255) NULL DEFAULT '' COMMENT '自定义样式',
  `created_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '创建用户ID',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_domain` (`domain`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='自定义页面信息表';

 */

class PwDesignPortalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_portal', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('标识ID');
            $table->string('pagename', 50)->nullable()->default('')->comment('页面名称');
            $table->string('title', 255)->nullable()->default('')->comment('title信息');
            $table->string('keywords', 255)->nullable()->default('')->comment('keywords信息');
            $table->string('description', 255)->nullable()->default('')->comment('description信息');
            $table->string('domain', 255)->nullable()->default('')->comment('二级域名');
            $table->string('cover', 255)->nullable()->default('')->comment('封面图片');
            $table->tinyInteger('isopen')->unsigned()->nullable()->default(0)->comment('是否使用');
            $table->tinyInteger('header')->unsigned()->nullable()->default(0)->comment('是否使用公共头');
            $table->tinyInteger('navigate')->unsigned()->nullable()->default(0)->comment('是否使用公共导航');
            $table->tinyInteger('footer')->unsigned()->nullable()->default(0)->comment('是否使用公共页脚');
            $table->string('style', 255)->nullable()->default('')->comment('自定义样式');
            $table->integer('created_uid')->unsigned()->nullable()->default(0)->comment('创建用户ID');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');

            $table->primary('id');
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_portal');
    }
}
