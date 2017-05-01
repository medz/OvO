<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_common_emotion`;
CREATE TABLE `pw_common_emotion` (
  `emotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '表情ID',
  `category_id` smallint(5) unsigned NULL DEFAULT '0' COMMENT '表情分类',
  `emotion_name` varchar(20) NULL DEFAULT '' COMMENT '表情名称',
  `emotion_folder` varchar(20) NULL DEFAULT '' COMMENT '所属文件夹',
  `emotion_icon` varchar(50) NULL DEFAULT '' COMMENT '表情图标',
  `vieworder` int(10) unsigned NULL DEFAULT '0' COMMENT '排序',
  `isused` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否使用',
  PRIMARY KEY (`emotion_id`),
  KEY `idx_catid` (`category_id`),
  KEY `idx_isused` (`isused`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表情数据表';

 */

class PwCommonEmotionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_common_emotion', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_common_emotion');
    }
}

