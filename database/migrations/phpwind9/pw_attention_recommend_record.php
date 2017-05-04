<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAttentionRecommendRecord extends Migration
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
          
            DROP TABLE IF EXISTS `pw_attention_recommend_record`;
            CREATE TABLE `pw_attention_recommend_record` (
              `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户uid',
              `recommend_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '潜在好友',
              `same_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '共同好友',
              UNIQUE KEY `idx_uid_puid_suid` (`uid`,`recommend_uid`,`same_uid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='共同关注记录表';

        */
        Schema::create('pw_attention_recommend_record', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户uid');
            $table->integer('recommend_uid')->unsigned()->nullable()->default(0)->comment('潜在好友');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('共同好友');
            $table->primary(['uid', 'recommend_uid' ,'same_uid']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_recommend_record');
    }
}
