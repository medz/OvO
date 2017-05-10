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
    public function up()
    {
        Schema::create('pw_user_behavior', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->default(0)->comment('用户UID');
            $table->char('behavior', 20)->comment('行为标识');
            $table->integer('number')->nullable()->default(0)->comment('行为统计');
            $table->integer('expired_time')->unsigned()->nullable()->default(0)->comment('过期时间');
            $table->string('extend_info', 255)->nullable()->default('')->comment('额外信息');

            $table->primary(['uid', 'behavior']);
        });
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
