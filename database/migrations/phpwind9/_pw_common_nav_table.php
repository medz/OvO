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
    public function run()
    {
        Schema::create('pw_common_nav', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_common_nav');
    }
}

