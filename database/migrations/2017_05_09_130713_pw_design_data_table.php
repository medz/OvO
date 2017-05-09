<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_data`;
CREATE TABLE `pw_design_data` (
  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '数据ID',
  `from_type` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '数据来源',
  `from_app` varchar(20) NULL DEFAULT '' COMMENT '来源应用名称',
  `from_id` int(10) unsigned NULL DEFAULT '0' COMMENT '数据来源ID',
  `module_id` int(10) unsigned NULL DEFAULT '0' COMMENT '所属模块ID',
  `standard` varchar(255) NULL DEFAULT '' COMMENT '标准标签',
  `style` varchar(255) NULL DEFAULT '' COMMENT '样式',
  `extend_info` text COMMENT '数据内容',
  `data_type` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '数据类型1自动 2固定 3修改',
  `is_edited` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否修改过',
  `is_reservation` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否为预订信息',
  `vieworder` int(10) unsigned NULL DEFAULT '0' COMMENT '排序',
  `start_time` int(10) unsigned NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`data_id`),
  KEY `idx_moduleid` (`module_id`),
  KEY `idx_vieworder` (`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='数据缓存表';

 */

class PwDesignDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_data', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('data_id')->unsigned()->comment('数据ID');
            $table->tinyInteger('from_type')->unsigned()->nullable()->default(0)->comment('数据来源');
            $table->string('from_app', 20)->nullable()->default('')->comment('来源应用名称');
            $table->integer('from_id')->unsigned()->nullable()->default(0)->comment('数据来源ID');
            $table->integer('module_id')->unsigned()->nullable()->default(0)->comment('所属模块ID');
            $table->string('standard', 255)->nullable()->default('')->comment('标准标签');
            $table->string('style', 255)->nullable()->default('')->comment('样式');
            $table->text('extend_info')->comment('数据内容');
            $table->tinyInteger('data_type')->unsigned()->nullable()->default(0)->comment('数据类型1自动 2固定 3修改');
            $table->tinyInteger('is_edited')->unsigned()->nullable()->default(0)->comment('是否修改过');
            $table->tinyInteger('is_reservation')->unsigned()->nullable()->default(0)->comment('是否为预订信息');
            $table->integer('vieworder')->unsigned()->nullable()->default(0)->comment('排序');
            $table->integer('start_time')->unsigned()->nullable()->default(0)->comment('开始时间');
            $table->integer('end_time')->unsigned()->nullable()->default(0)->comment('过期时间');
            $table->primary('data_id');
            $table->index('module_id');
            $table->index('vieworder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_data');
    }
}
