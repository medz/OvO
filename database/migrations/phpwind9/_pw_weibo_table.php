<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_weibo`;
CREATE TABLE `pw_weibo` (
  `weibo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `src_id` int(10) unsigned NULL DEFAULT '0',
  `content` text,
  `type` tinyint(3) unsigned NULL DEFAULT '0',
  `comments` mediumint(8) unsigned NULL DEFAULT '0',
  `extra` text,
  `like_count` mediumint(8) unsigned NULL DEFAULT '0',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_username` varchar(15) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`weibo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='微薄表';

 */

class PwWeiboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_weibo', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_weibo');
    }
}

