<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_register_check`;
CREATE TABLE `pw_user_register_check` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `ifchecked` tinyint(1) NULL DEFAULT '1' COMMENT '是否已经审核',
  `ifactived` tinyint(1) NULL DEFAULT '1' COMMENT '是否已经激活',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户注册审核记录表';

 */

class PwUserRegisterCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_user_register_check', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_register_check');
    }
}

