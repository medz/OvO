<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_task_group`;
CREATE TABLE `pw_task_group` (
  `taskid` int(10) unsigned NOT NULL COMMENT '任务ID',
  `gid` int(10) NOT NULL COMMENT '用户组ID',
  `is_auto` tinyint(1) NULL DEFAULT '0' COMMENT '是否是周期任务',
  `end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '结束时间',
  PRIMARY KEY (`gid`,`taskid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务索引';

 */

class PwTaskGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_task_group', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('taskid')->unsigned()->nullable()->default(0)->comment('任务ID');
            $table->integer('gid')->default(0)->comment('用户组ID');
            $table->tinyInteger('is_auto')->nullable()->default(0)->comment('是否是周期任务');
            $table->integer('end_time')->unsigned()->nullable()->default(0)->comment('举报人');

            $table->primary(['gid','taskid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_task_group');
    }
}

