<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAttentionRecommendCron extends Migration
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
        
          DROP TABLE IF EXISTS `pw_attention_recommend_cron`;
          CREATE TABLE `pw_attention_recommend_cron` (
            `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
            `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
            PRIMARY KEY (`uid`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可能认识的人更新任务表';


        */
        Schema::create('pw_attention_recommend_cron', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->comment('用户uid');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->primary('uid');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_recommend_cron');
    }
}
