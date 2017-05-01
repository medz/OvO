<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_like_statistics`;
CREATE TABLE `pw_like_statistics` (
  `signkey` varchar(20) NOT NULL COMMENT '标识key',
  `likeid` int(10) unsigned NULL DEFAULT '0' COMMENT '喜欢ID',
  `typeid` int(10) unsigned NULL DEFAULT '0' COMMENT '类型ID',
  `fromid` int(10) unsigned NULL DEFAULT '0' COMMENT '来源ID',
  `number` int(10) unsigned NULL DEFAULT '0' COMMENT '数量',
  KEY `idx_number` (`number`),
  KEY `idx_signkey` (`signkey`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='喜欢静态数据表';

 */

class PwLikeStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_like_statistics', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_like_statistics');
    }
}

