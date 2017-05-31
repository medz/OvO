<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_component`;
CREATE TABLE `pw_design_component` (
  `comp_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '元件ID',
  `model_flag` varchar(20) NULL DEFAULT '' COMMENT '元件类型标识',
  `comp_name` varchar(50) NULL DEFAULT '' COMMENT '模版元件名称',
  `comp_tpl` text COMMENT '模版代码',
  `sys_id` int(10) unsigned NULL DEFAULT '0' COMMENT '系统编号',
  PRIMARY KEY (`comp_id`),
  KEY `idx_modelflag` (`model_flag`(10))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模版元件表';

 */

class PwDesignComponentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_component', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('comp_id')->unsigned()->comment('元件ID');
            $table->string('model_flag', 20)->nullable()->default('')->comment('元件类型标识');
            $table->string('comp_name', 50)->nullable()->default('')->comment('模版元件名称');
            $table->text('comp_tpl')->comment('模版代码');
            $table->integer('sys_id')->unsigned()->nullable()->comment('系统编号');

            $table->index('model_flag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_component');
    }
}
