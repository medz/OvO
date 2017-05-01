<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*


DROP TABLE IF EXISTS `pw_attachs_thread`;
CREATE TABLE `pw_attachs_thread` (
  `aid` int(10) unsigned NOT NULL COMMENT '附件id',
  `fid` smallint(5) unsigned NULL DEFAULT '0' COMMENT '所属版块id',
  `tid` int(10) unsigned NULL DEFAULT '0' COMMENT '所属帖子id',
  `pid` int(10) unsigned NULL DEFAULT '0' COMMENT '所属回复id',
  `name` varchar(80) NULL DEFAULT '' COMMENT '文件名',
  `type` varchar(15) NULL DEFAULT '' COMMENT '文件类型',
  `size` int(10) unsigned NULL DEFAULT '0' COMMENT '文件大小',
  `hits` int(10) unsigned NULL DEFAULT '0' COMMENT '下载数',
  `width` smallint(5) unsigned NULL DEFAULT '0' COMMENT '图片宽度',
  `height` smallint(5) unsigned NULL DEFAULT '0' COMMENT '图片高度',
  `path` varchar(80) NULL DEFAULT '' COMMENT '存储路径',
  `ifthumb` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否有缩略图',
  `special` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否售密',
  `cost` int(10) unsigned NULL DEFAULT '0' COMMENT '售密价格',
  `ctype` smallint(5) unsigned NULL DEFAULT '0' COMMENT '积分类型',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '上传人用户id',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '上传时间',
  `descrip` varchar(255) NULL DEFAULT '' COMMENT '文件描述',
  PRIMARY KEY (`aid`),
  KEY `idx_createduserid` (`created_userid`),
  KEY `idx_tid_pid` (`tid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子附件表';

 */

class PwAttachsThreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_attachs_thread', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_attachs_thread');
    }
}

