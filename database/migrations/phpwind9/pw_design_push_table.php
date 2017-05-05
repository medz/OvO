<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_push`;
CREATE TABLE `pw_design_push` (
  `push_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '推送ID',
  `push_from_id` int(10) unsigned NULL DEFAULT '0' COMMENT '来源ID',
  `push_from_model` varchar(20) NULL DEFAULT '' COMMENT '来源应用',
  `module_id` int(10) unsigned NULL DEFAULT '0' COMMENT '所属模块ID',
  `push_standard` varchar(255) NULL DEFAULT '' COMMENT '标准化标签',
  `push_style` varchar(255) NULL DEFAULT '' COMMENT '样式',
  `push_orderid` int(10) unsigned NULL DEFAULT '0' COMMENT '排序',
  `push_extend` text COMMENT '推送内容',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '推送人ID',
  `author_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '作者UID',
  `status` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '状态显示：0 需要审核：1',
  `neednotice` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否需要发送站内信',
  `check_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '审核人ID',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '推送时间',
  `start_time` int(10) unsigned NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '过期时间',
  `checked_time` int(10) unsigned NULL DEFAULT '0' COMMENT '审核时间',
  PRIMARY KEY (`push_id`),
  KEY `idx_end_time` (`end_time`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='信息推送表';

 */

class PwDesignPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_push', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('push_id')->unsigned()->comment('推送ID');
            $table->integer('push_from_id')->unsigned()->nullable()->default(0)->comment('来源ID');
            $table->string('push_from_model', 20)->nullable()->default('')->comment('来源应用');
            $table->integer('module_id')->unsigned()->nullable()->default(0)->comment('所属模块ID');
            $table->string('push_standard', 255)->nullable()->default('')->comment('标准化标签');
            $table->string('push_style', 255)->default('')->comment('样式');
            $table->integer('push_orderid')->unsigned()->nullable()->default(0)->comment('排序');
            $table->text('push_extend')->comment('推送内容');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('推送人ID');
            $table->integer('author_uid')->unsigned()->nullable()->default(0)->comment('作者UID');
            $table->tinyInteger('status')->unsigned()->nullable()->default(0)->comment('状态显示：0 需要审核：1');
            $table->tinyInteger('neednotice')->unsigned()->nullable()->default(0)->comment('是否需要发送站内信');
            $table->integer('check_uid')->unsigned()->nullable()->default(0)->comment('审核人ID');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('推送时间');
            $table->integer('start_time')->unsigned()->nullable()->default(0)->comment('开始时间');
            $table->integer('end_time')->unsigned()->nullable()->default(0)->comment('过期时间');
            $table->integer('checked_time')->unsigned()->nullable()->default(0)->comment('审核时间');
            $table->primary('push_id');
            $table->index('end_time');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_push');
    }
}
