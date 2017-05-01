<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_data`;
CREATE TABLE `pw_user_data` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `lastvisit` int(10) unsigned NULL DEFAULT '0' COMMENT '最后访问时间',
  `lastloginip` varchar(20) NULL DEFAULT '' COMMENT '最后登录IP',
  `lastpost` int(10) unsigned NULL DEFAULT '0' COMMENT '最后发帖时间',
  `lastactivetime` int(10) unsigned NULL DEFAULT '0' COMMENT '最后活动时间',
  `onlinetime` int(10) unsigned NULL DEFAULT '0' COMMENT '在线时长',
  `trypwd` varchar(16) NULL DEFAULT '' COMMENT '尝试的登录错误信息，trydate|trynum',
  `postcheck` varchar(16) NULL DEFAULT '' COMMENT '发帖检查',
  `postnum` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '发帖数',
  `digest` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '精华数',
  `todaypost` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '今天发帖数',
  `todayupload` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '今日上传个数',
  `findpwd` varchar(26) NULL DEFAULT '' COMMENT '找回密码尝试错误次数,trydate|trynum',
  `follows` int(10) unsigned NULL DEFAULT '0' COMMENT '关注数',
  `fans` int(10) unsigned NULL DEFAULT '0' COMMENT '粉丝数',
  `message_tone` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否有新消息',
  `messages` smallint(5) unsigned NULL DEFAULT '0' COMMENT '私信数',
  `notices` smallint(5) unsigned NULL DEFAULT '0' COMMENT '消息数',
  `likes` int(10) unsigned NULL DEFAULT '0' COMMENT '喜欢次数',
  `punch` varchar(200) NULL DEFAULT '' COMMENT '打卡相关',
  `credit1` int(10) NULL DEFAULT '0' COMMENT '积分字段1',
  `credit2` int(10) NULL DEFAULT '0' COMMENT '积分字段2',
  `credit3` int(10) NULL DEFAULT '0' COMMENT '积分字段3',
  `credit4` int(10) NULL DEFAULT '0' COMMENT '积分字段4',
  `credit5` int(10) NULL DEFAULT '0',
  `credit6` int(10) NULL DEFAULT '0',
  `credit7` int(10) NULL DEFAULT '0',
  `credit8` int(10) NULL DEFAULT '0',
  `join_forum` varchar(255) NULL DEFAULT '' COMMENT '加入的版块',
  `recommend_friend` varchar(255) NULL DEFAULT '' COMMENT '推荐朋友',
  `last_credit_affect_log` varchar(255) NULL DEFAULT '' COMMENT '最后积分变动内容',
  `medal_ids` varchar(255) NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户扩展数据表';

 */

class PwUserDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_user_data', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_data');
    }
}

