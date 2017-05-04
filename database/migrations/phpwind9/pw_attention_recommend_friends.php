<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAttentionRecommendFriends extends Migration
{
    /**
     * 迁移运行.
     *
     * @return void
     * @author 流星 <lkddi@163.com>
     */
    public function run()
    {
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
        Schema::create('pw_attention_recommend_friends', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户uid');
            $table->integer('recommend_uid')->unsigned()->nullable()->default(0)->comment('推荐好友ID');
            $table->string('recommend_username', 15)->nullable()->default('')->comment('推荐好友用户名');
            $table->smallInteger('cnt')->unsigned()->nullable()->default(0)->comment('好友数量');
            $table->text('recommend_user')->comment('推荐好友信息');
            $table->primary(['uid', 'recommend_uid']);

        });
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
