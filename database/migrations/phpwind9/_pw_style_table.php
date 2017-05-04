<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_style`;
CREATE TABLE `pw_style` (
  `app_id` char(20) NOT NULL,
  `iscurrent` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否默认',
  `style_type` char(10) NULL DEFAULT '' COMMENT '风格类型',
  `name` varchar(100) NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(100) NULL DEFAULT '' COMMENT '应用别名',
  `logo` varchar(100) NULL DEFAULT '' COMMENT '图标',
  `author_name` varchar(30) NULL DEFAULT '' COMMENT '作者名',
  `author_icon` varchar(100) NULL DEFAULT '' COMMENT '作者头像',
  `author_email` varchar(200) NULL DEFAULT '' COMMENT '作者email',
  `website` varchar(200) NULL DEFAULT '' COMMENT '作者网站',
  `version` varchar(50) NULL DEFAULT '' COMMENT '应用版本',
  `pwversion` varchar(50) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题风格表';

 */

class PwStyleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_style', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_style');
    }
}

