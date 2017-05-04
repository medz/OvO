<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_register_ip`;
CREATE TABLE `pw_user_register_ip` (
  `ip` varchar(20) NOT NULL COMMENT 'IP地址',
  `last_regdate` int(10) unsigned NULL DEFAULT '0' COMMENT '最后注册时间',
  `num` int(10) unsigned NULL DEFAULT '0' COMMENT '次数',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='登录的IP统计表';

 */

class PwUserRegisterIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_register_ip', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
			
			$table->string('ip', 20)->comment('IP地址');
			$table->integer('last_regdate')->unsigned()->nullable()->default(0)->comment('最后注册时间');
			$table->integer('num')->unsigned()->nullable()->default(0)->comment('次数');
			
			$table->primary('ip');		
			
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_register_ip');
    }
}

