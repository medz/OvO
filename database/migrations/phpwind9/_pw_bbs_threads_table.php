<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_threads`;
CREATE TABLE `pw_bbs_threads` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` smallint(5) unsigned NULL DEFAULT '0',
  `topic_type` int(10) unsigned NULL DEFAULT '0',
  `subject` varchar(100) NULL DEFAULT '',
  `overtime` int(10) unsigned NULL DEFAULT '0',
  `highlight` varchar(64) NULL DEFAULT '',
  `inspect` varchar(30) NULL DEFAULT '',
  `ifshield` tinyint(1) unsigned NULL DEFAULT '0',
  `digest` tinyint(3) unsigned NULL DEFAULT '0',
  `topped` tinyint(3) unsigned NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NULL DEFAULT '0',
  `ischeck` tinyint(3) NULL DEFAULT '1',
  `replies` int(10) unsigned NULL DEFAULT '0',
  `hits` int(10) unsigned NULL DEFAULT '0',
  `like_count` mediumint(8) unsigned NULL DEFAULT '0',
  `special` varchar(20) NULL DEFAULT '0',
  `tpcstatus` int(10) unsigned NULL DEFAULT '0',
  `ifupload` tinyint(3) unsigned NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `created_username` varchar(15) NULL DEFAULT '',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_ip` varchar(40) NULL DEFAULT '',
  `modified_time` int(10) unsigned NULL DEFAULT '0',
  `modified_username` varchar(15) NULL DEFAULT '',
  `modified_userid` int(10) unsigned NULL DEFAULT '0',
  `modified_ip` varchar(40) NULL DEFAULT '',
  `lastpost_time` int(10) unsigned NULL DEFAULT '0',
  `lastpost_userid` int(10) unsigned NULL DEFAULT '0',
  `lastpost_username` varchar(15) NULL DEFAULT '',
  `special_sort` tinyint(4) NULL DEFAULT '0',
  `reply_notice` tinyint(3) unsigned NULL DEFAULT '1',
  `reply_topped` mediumint(8) unsigned NULL DEFAULT '0',
  `thread_status` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `idx_fid_disabled_lastposttime` (`fid`,`disabled`,`lastpost_time`),
  KEY `idx_disabled_createdtime` (`disabled`,`created_time`),
  KEY `idx_createduserid_createdtime` ( `created_userid` , `created_time` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子基本信息表';

 */

class PwBbsThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_threads', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_threads');
    }
}

