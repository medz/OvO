<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_shield`;
CREATE TABLE `pw_design_shield` (
  `shield_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识ID',
  `from_app` varchar(20) NULL DEFAULT '' COMMENT '来源应用名称',
  `from_id` int(10) unsigned NULL DEFAULT '0' COMMENT '来源ID',
  `module_id` int(10) unsigned NULL DEFAULT '0' COMMENT '被屏蔽的模块',
  `shield_title` varchar(255) NULL DEFAULT '',
  `shield_url` varchar(255) NULL DEFAULT '',
  PRIMARY KEY (`shield_id`),
  KEY `idx_formid_formapp` (`from_id`,`from_app`(5))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='门户模块数据屏蔽表';

 */

class PwDesignShieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_design_shield', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_design_shield');
    }
}

