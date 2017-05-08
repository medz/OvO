<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_mobile`;
CREATE TABLE `pw_user_mobile` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `mobile` bigint(11) unsigned NULL DEFAULT '0' COMMENT '用户手机号码',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户手机验证表';

 */

class PwUserMobileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_mobile', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->comment('用户uid');
            $table->bigInteger('mobile')->unsigned()->nullable()->default(0)->comment('举报时间');

            $table->primary('uid');
            $table->index('mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_mobile');
    }
}

