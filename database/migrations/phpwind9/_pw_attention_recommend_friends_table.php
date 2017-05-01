<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention_recommend_friends`;
CREATE TABLE `pw_attention_recommend_friends` (
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户uid',
  `recommend_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '推荐好友ID',
  `recommend_username` varchar(15) NULL DEFAULT '' COMMENT '推荐好友用户名',
  `cnt` smallint(5) unsigned NULL DEFAULT '0' COMMENT '好友数量',
  `recommend_user` text COMMENT '推荐好友信息',
  UNIQUE KEY `idx_uid_recommenduid` (`uid`,`recommend_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可能认识的人缓存表';

 */

class PwAttentionRecommendFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_attention_recommend_friends', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_attention_recommend_friends');
    }
}

