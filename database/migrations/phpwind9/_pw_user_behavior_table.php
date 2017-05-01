<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_behavior`;
CREATE TABLE `pw_user_behavior` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
  `behavior` char(20) NOT NULL COMMENT '行为标识',
  `number` int(10) NULL DEFAULT '0' COMMENT '行为统计',
  `expired_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  `extend_info` varchar(255) NULL DEFAULT '' COMMENT '额外信息',
  PRIMARY KEY (`uid`,`behavior`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户行为统计表';

 */

class PwUserBehaviorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_user_behavior', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_behavior');
    }
}

