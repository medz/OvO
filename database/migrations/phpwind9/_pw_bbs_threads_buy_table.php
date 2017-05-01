<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_buy`;
CREATE TABLE `pw_bbs_threads_buy` (
  `tid` int(10) unsigned NULL DEFAULT '0',
  `pid` int(10) unsigned NULL DEFAULT '0',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `ctype` tinyint(3) unsigned NULL DEFAULT '0',
  `cost` mediumint(8) unsigned NULL DEFAULT '0',
  KEY `idx_tid_pid_createdtime` (`tid`,`pid`,`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子购买记录贴表';

 */

class PwBbsThreadsBuyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_threads_buy', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_threads_buy');
    }
}

