<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_forum`;
CREATE TABLE `pw_bbs_forum` (
  `fid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(5) unsigned NULL DEFAULT '0',
  `type` enum('category','forum','sub','sub2') NULL DEFAULT 'forum',
  `issub` tinyint(1) unsigned NULL DEFAULT '0',
  `hassub` tinyint(1) unsigned NULL DEFAULT '0',
  `name` varchar(255) NULL DEFAULT '',
  `descrip` text,
  `vieworder` smallint(5) unsigned NULL DEFAULT '0',
  `across` tinyint(3) unsigned NULL DEFAULT '0',
  `manager` varchar(255) NULL DEFAULT '',
  `uppermanager` varchar(255) NULL DEFAULT '',
  `icon` varchar(100) NULL DEFAULT '',
  `logo` varchar(100) NULL DEFAULT '',
  `fup` varchar(30) NULL DEFAULT '',
  `fupname` varchar(255) NULL DEFAULT '',
  `isshow` tinyint(1) unsigned NULL DEFAULT '1',
  `isshowsub` tinyint(1) unsigned NULL DEFAULT '0',
  `newtime` smallint(5) unsigned NULL DEFAULT '0',
  `password` varchar(32) NULL DEFAULT '',
  `allow_visit` varchar(255) NULL DEFAULT '',
  `allow_read` varchar(255) NULL DEFAULT '',
  `allow_post` varchar(255) NULL DEFAULT '',
  `allow_reply` varchar(255) NULL DEFAULT '',
  `allow_upload` varchar(255) NULL DEFAULT '',
  `allow_download` varchar(255) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `created_username` varchar(15) NULL DEFAULT '',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_ip` int(10) unsigned NULL DEFAULT '0',
  `style` varchar(20) NULL DEFAULT '',
  PRIMARY KEY (`fid`),
  KEY `idx_issub_vieworder` (`issub`,`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='版块基本信息表';

 */

class PwBbsForumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_forum', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_forum');
    }
}

