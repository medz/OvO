<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_forum_statistics`;
CREATE TABLE `pw_bbs_forum_statistics` (
  `fid` smallint(5) unsigned NOT NULL,
  `todayposts` mediumint(8) unsigned NULL DEFAULT '0',
  `todaythreads` mediumint(8) unsigned NULL DEFAULT '0',
  `article` mediumint(8) unsigned NULL DEFAULT '0',
  `posts` int(10) unsigned NULL DEFAULT '0',
  `threads` mediumint(8) unsigned NULL DEFAULT '0',
  `subposts` mediumint(8) unsigned NULL DEFAULT '0',
  `subthreads` mediumint(8) unsigned NULL DEFAULT '0',
  `lastpost_info` char(35) NULL DEFAULT '',
  `lastpost_time` int(10) unsigned NULL DEFAULT '0',
  `lastpost_username` varchar(15) NULL DEFAULT '',
  `lastpost_tid` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='版块统计表';

 */

class PwBbsForumStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_forum_statistics', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_forum_statistics');
    }
}

