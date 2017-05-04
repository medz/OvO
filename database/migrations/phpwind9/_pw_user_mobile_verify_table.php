<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_mobile_verify`;
CREATE TABLE `pw_user_mobile_verify` (
  `mobile` bigint(11) unsigned NOT NULL COMMENT '用户手机号码',
  `code` smallint(5) unsigned NULL DEFAULT '0' COMMENT '验证码',
  `expired_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  `number` tinyint(3) unsigned NULL DEFAULT '0',
  `create_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户手机验证表';

 */

class PwUserMobileVerifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_mobile_verify', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_mobile_verify');
    }
}

