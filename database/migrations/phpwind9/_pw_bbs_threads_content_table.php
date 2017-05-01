<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_content`;
CREATE TABLE `pw_bbs_threads_content` (
  `tid` int(10) unsigned NOT NULL,
  `useubb` tinyint(1) unsigned NULL DEFAULT '0',
  `usehtml` tinyint(1) unsigned NULL DEFAULT '0',
  `aids` smallint(5) unsigned NULL DEFAULT '0',
  `content` text,
  `sell_count` mediumint(8) unsigned NULL DEFAULT '0',
  `reminds` varchar(255) NULL DEFAULT '',
  `word_version` smallint(5) unsigned NULL DEFAULT '0',
  `tags` varchar(255) NULL DEFAULT '',
  `ipfrom` varchar(255) NULL DEFAULT '',
  `manage_remind` varchar(150) NULL DEFAULT '',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子内容表';

 */

class PwBbsThreadsContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_threads_content', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_threads_content');
    }
}

