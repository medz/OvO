<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_task`;
CREATE TABLE `pw_task` (
  `taskid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务ID',
  `pre_task` int(10) unsigned NULL DEFAULT '0' COMMENT '前置任务ID',
  `is_auto` tinyint(1) NULL DEFAULT '0' COMMENT '是否是自动任务标识',
  `is_display_all` tinyint(1) NULL DEFAULT '0' COMMENT '是否显示给所有用户',
  `view_order` smallint(6) NULL DEFAULT '0' COMMENT '顺序',
  `is_open` tinyint(3) NULL DEFAULT '0' COMMENT '是否开启状态',
  `start_time` int(10) unsigned NULL DEFAULT '0' COMMENT '开始的时间',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '结束时间',
  `period` smallint(6) NULL DEFAULT '0' COMMENT '是否是周期任务',
  `title` varchar(100) NULL DEFAULT '' COMMENT '标题',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  `icon` varchar(200) NULL DEFAULT '' COMMENT '图标路径',
  `user_groups` varchar(255) NULL DEFAULT '-1' COMMENT '可以申请任务的用户组',
  `reward` varchar(255) NULL DEFAULT '' COMMENT '奖励',
  `conditions` varchar(255) NULL DEFAULT '' COMMENT '完成条件',
  PRIMARY KEY (`taskid`),
  KEY `idx_pretask` (`pre_task`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='任务';

 */

class PwTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_task', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('taskid')->unsigned()->comment('话题id');
            $table->integer('pre_task')->unsigned()->nullable()->default(0)->comment('前置任务ID');
            $table->tinyInteger('is_auto')->nullable()->default(0)->comment('是否是自动任务标识');
            $table->tinyInteger('is_display_all')->nullable()->default(0)->comment('是否显示给所有用户');
            $table->smallinteger('view_order')->nullable()->default(0)->comment('顺序');
            $table->tinyInteger('is_open')->nullable()->default(0)->comment('是否开启状态');
            $table->integer('start_time')->unsigned()->nullable()->default(0)->comment('开始的时间');
            $table->integer('end_time')->unsigned()->nullable()->default(0)->comment('结束时间');
            $table->smallinteger('period')->nullable()->default(0)->comment('是否是周期任务');
            $table->string('title', 100)->nullable()->default('')->comment('标题');
            $table->string('description', 255)->nullable()->default('')->comment('描述');
            $table->string('icon', 200)->nullable()->default('')->comment('图标路径');
            $table->string('user_groups', 255)->nullable()->default('-1')->comment('可以申请任务的用户组');
            $table->string('reward', 255)->nullable()->default('')->comment('奖励');
            $table->string('conditions', 255)->nullable()->default('')->comment('完成条件');

            $table->primary('taskid');
            $table->index('pre_task');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_task');
    }
}

