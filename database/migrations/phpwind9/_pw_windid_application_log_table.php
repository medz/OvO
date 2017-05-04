<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_application_log`;
CREATE TABLE `pw_windid_application_log` (
  `app_id` char(20) NULL DEFAULT '' COMMENT '应用id',
  `log_type` char(10) NULL DEFAULT '' COMMENT '日志类型',
  `data` text COMMENT '日志内容',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  UNIQUE KEY `app_id` (`app_id`,`log_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用安装日志表';

 */

class PwWindidApplicationLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_application_log', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_windid_application_log');
    }
}

