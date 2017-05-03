<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAppPollVoter extends Migration
{
    /**
     * 迁移运行.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        /*
			DROP TABLE IF EXISTS `pw_app_poll_voter`;
	        CREATE TABLE `pw_app_poll_voter` (
	          `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '投票参与人ID',
	          `poll_id` int(10) unsigned NULL DEFAULT '0' COMMENT '投票ID',
	          `option_id` int(10) unsigned NULL DEFAULT '0' COMMENT '投票选项ID',
	          `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '参与投票时间',
	          KEY `idx_uid_createdtime` (`uid`,`created_time`),
	          KEY `idx_pollid` (`poll_id`)
	        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户投票记录表';         
        */
        Schema::create('pw_app_poll_voter', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('uid')>unsigned()->nullable()->default(0)->comment('投票参与人ID');
            $table->integer('poll_id')->unsigned()->nullable()->default(0)->comment('投票ID');
            $table->integer('option_id')->unsigned()->nullable()->default(0)->comment('投票选项ID');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('参与投票时间');
            $table->index('poll_id');
            $table->index(['uid', 'created_time']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_app_poll_voter');
    }
}
