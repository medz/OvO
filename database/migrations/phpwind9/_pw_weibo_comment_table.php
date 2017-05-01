<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_weibo_comment`;
CREATE TABLE `pw_weibo_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weibo_id` int(10) unsigned NULL DEFAULT '0',
  `content` text,
  `extra` text,
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_username` varchar(15) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `idx_weiboid_createdtime` (`weibo_id`,`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='微薄评论表';

 */

class PwWeiboCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_weibo_comment', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_weibo_comment');
    }
}

