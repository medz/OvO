<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_task_user`;
CREATE TABLE `pw_task_user` (
  `taskid` int(10) unsigned NOT NULL COMMENT '任务ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `task_status` tinyint(3) NULL DEFAULT '0' COMMENT '任务状态',
  `is_period` tinyint(1) NULL DEFAULT '0' COMMENT '是否是周期任务',
  `step` varchar(100) NULL DEFAULT '' COMMENT '任务完成的进度信息',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '申请任务时间',
  `finish_time` int(10) unsigned NULL DEFAULT '0' COMMENT '完成任务时间',
  PRIMARY KEY (`uid`,`taskid`),
  KEY `idx_uid_taskstatus` (`uid`,`task_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务用户';

 */

class PwTaskUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_task_user', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('taskid')->unsigned()->comment('任务ID');
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户ID');
            $table->tinyInteger('task_status')->nullable()->default(0)->comment('任务状态');
            $table->tinyInteger('is_period')->nullable()->default(0)->comment('是否是周期任务');
            $table->string('step', 100)->nullable()->default('')->comment('任务完成的进度信息');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('申请任务时间');
            $table->integer('finish_time')->unsigned()->nullable()->default(0)->comment('完成任务时间');

            $table->primary(['uid', 'taskid']);
            $table->index(['uid', 'task_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_task_user');
    }
}

