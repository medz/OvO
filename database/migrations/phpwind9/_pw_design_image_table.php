<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_image`;
CREATE TABLE `pw_design_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `path` varchar(80) NULL DEFAULT '' COMMENT '原图片路径',
  `thumb` varchar(80) NULL DEFAULT '' COMMENT '缩略图路径',
  `width` int(10) unsigned NULL DEFAULT '0' COMMENT '缩略图宽',
  `height` int(10) unsigned NULL DEFAULT '0' COMMENT '缩略图高',
  `moduleid` int(10) unsigned NULL DEFAULT '0' COMMENT '所属模块',
  `data_id` int(10) unsigned NULL DEFAULT '0' COMMENT '门户数据ID',
  `sign` varchar(50) NULL DEFAULT '' COMMENT '标签key',
  `status` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '原图片状态1正常0不正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户异步缩略图片表';

 */

class PwDesignImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_design_image', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_design_image');
    }
}

