<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_user_data`;
CREATE TABLE `pw_windid_user_data` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `messages` smallint(6) unsigned NULL DEFAULT '0' COMMENT '用户消息数',
  `credit1` int(10) NULL DEFAULT '0' COMMENT '积分1',
  `credit2` int(10) NULL DEFAULT '0' COMMENT '积分2',
  `credit3` int(10) NULL DEFAULT '0' COMMENT '积分3',
  `credit4` int(10) NULL DEFAULT '0' COMMENT '积分4',
  `credit5` int(10) NULL DEFAULT '0',
  `credit6` int(10) NULL DEFAULT '0',
  `credit7` int(10) NULL DEFAULT '0',
  `credit8` int(10) NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='windid用户数据';

 */

class PwWindidUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_user_data', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_windid_user_data');
    }
}

