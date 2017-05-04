<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_login_ip_recode`;
CREATE TABLE `pw_user_login_ip_recode` (
  `ip` varchar(20) NOT NULL COMMENT 'IP地址',
  `last_time` varchar(10) NULL DEFAULT '' COMMENT '最后访问时间',
  `error_count` smallint(5) unsigned NULL DEFAULT '0' COMMENT '错误次数',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户IP登录记录表-用户IP登录限制';

 */

class PwUserLoginIpRecodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_login_ip_recode', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_login_ip_recode');
    }
}

