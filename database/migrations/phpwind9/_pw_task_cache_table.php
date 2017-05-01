<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_task_cache`;
CREATE TABLE `pw_task_cache` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `task_ids` varchar(200) NULL DEFAULT '' COMMENT '该用户完成任务的最后ID记录及周期任务ID记录',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务缓存';

 */

class PwTaskCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_task_cache', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_task_cache');
    }
}

