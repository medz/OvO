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
            
            $table->integer('uid')->unsigned()->comment('用户ID');
            $table->smallInteger('messages')->unsigned()->nullable()->default(0)->comment('用户消息数');
            $table->integer('credit1')->nullable()->default(0)->comment('积分1');
            $table->integer('credit2')->nullable()->default(0)->comment('积分2');
            $table->integer('credit3')->nullable()->default(0)->comment('积分3');
            $table->integer('credit4')->nullable()->default(0)->comment('积分4');
            $table->integer('credit5')->nullable()->default(0);
            $table->integer('credit6')->nullable()->default(0);
            $table->integer('credit7')->nullable()->default(0);
            $table->integer('credit8')->nullable()->default(0);
            
            $table->primary('uid');
        });
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

