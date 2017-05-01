<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_common_emotion_category`;
CREATE TABLE `pw_common_emotion_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `category_name` varchar(20) NULL DEFAULT '' COMMENT '分类名',
  `emotion_folder` varchar(20) NULL DEFAULT '' COMMENT '分类文件夹',
  `emotion_apps` varchar(50) NULL DEFAULT '' COMMENT '能使用的应用',
  `orderid` int(10) unsigned NULL DEFAULT '0' COMMENT '排序',
  `isopen` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否使用',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表情分类表';

 */

class PwCommonEmotionCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_common_emotion_category', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_common_emotion_category');
    }
}

