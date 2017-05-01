<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_script`;
CREATE TABLE `pw_design_script` (
  `module_id` int(10) unsigned NOT NULL COMMENT '模块ID',
  `token` char(10) NOT NULL COMMENT '加密串',
  `view_times` int(10) unsigned NULL DEFAULT '0' COMMENT '调用次数',
  PRIMARY KEY (`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块调用管理表';

 */

class PwDesignScriptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_design_script', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_design_script');
    }
}

