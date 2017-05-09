<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_common_nav`;
CREATE TABLE `pw_common_nav` (
  `navid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `parentid` int(10) unsigned NULL DEFAULT '0' COMMENT '导航上级ID',
  `rootid` int(10) unsigned NULL DEFAULT '0' COMMENT '导航类ID',
  `type` varchar(32) NULL DEFAULT '' COMMENT '所属类型',
  `sign` varchar(32) NULL DEFAULT '' COMMENT '当前定位标识',
  `name` char(50) NULL DEFAULT '' COMMENT '导航名称',
  `style` char(50) NULL DEFAULT '' COMMENT '导航样式',
  `link` char(100) NULL DEFAULT '' COMMENT '导航链接',
  `alt` char(50) NULL DEFAULT '' COMMENT '链接ALT信息',
  `image` varchar(100) NULL DEFAULT '' COMMENT '导航小图标',
  `target` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否新窗口打开',
  `isshow` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否使用',
  `orderid` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`navid`),
  KEY `idx_type` (`type`),
  KEY `idx_rootid` (`rootid`),
  KEY `idx_orderid` (`orderid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='导航表';

 */

class PwCommonNavTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_common_nav', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('navid')->unsigned()->comment('导航ID');
            $table->integer('parentid')->unsigned()->nullable()->default(0)->comment('导航上级ID');
            $table->integer('rootid')->unsigned()->nullable()->default(0)->comment('导航类ID');
            $table->string('type', 32)->nullable()->default('')->comment('所属类型');
            $table->string('sign', 32)->nullable()->default('')->comment('当前定位标识');
            $table->char('name', 50)->nullable()->default('')->comment('导航名称');
            $table->char('style', 50)->nullable()->default('')->comment('导航样式');
            $table->char('link', 100)->nullable()->default('')->comment('导航链接');
            $table->char('alt', 50)->nullable()->default('')->comment('链接ALT信息');
            $table->string('image', 100)->nullable()->default('')->comment('导航小图标');
            $table->tinyInteger('target')->unsigned()->nullable()->default(0)->comment('是否新窗口打开');
            $table->tinyInteger('isshow')->unsigned()->nullable()->default(0)->comment('是否使用');
            $table->tinyInteger('orderid')->unsigned()->nullable()->default(0)->comment('排序');

            $table->primary('navid');
            $table->index('type');
            $table->index('rootid');
            $table->index('orderid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_common_nav');
    }
}
