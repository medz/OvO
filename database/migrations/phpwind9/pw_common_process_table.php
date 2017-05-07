<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_common_process`;
CREATE TABLE `pw_common_process` (
  `flag` varchar(20) NOT NULL COMMENT '进程标记',
  `uid` int(10) unsigned NOT NULL COMMENT '进程锁用户',
  `expired_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`flag`(10),`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='进程控制表';

 */

class PwCommonProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_common_process', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
			
			$table->string('flag', 20)->comment('进程标记');
 			$table->integer('uid')->unsigned()->comment('进程锁用户');
 			$table->integer('expired_time')->unsigned()->nullable()->default(0)->comment('过期时间');
 
			$table->primary('flag','uid');
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_common_process');
    }
}

