<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_overtime`;
CREATE TABLE `pw_bbs_threads_overtime` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NULL DEFAULT '0',
  `m_type` enum('topped','highlight') NOT NULL,
  `overtime` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tid_mtype` (`tid`,`m_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子操作时间表';

 */

class PwBbsThreadsOvertimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_threads_overtime', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_threads_overtime');
    }
}

