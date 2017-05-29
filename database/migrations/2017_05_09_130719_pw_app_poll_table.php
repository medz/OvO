<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAppPollTable extends Migration
{
    /**
     * 迁移运行.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function up()
    {
        /*
            DROP TABLE IF EXISTS `pw_app_poll`;
            CREATE TABLE `pw_app_poll` (
              `poll_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
              `voter_num` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '投票人数',
              `isafter_view` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否投票后查看结果',
              `isinclude_img` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否包含图片',
              `option_limit` smallint(5) unsigned NULL DEFAULT '0' COMMENT '投票选项控制',
              `regtime_limit` int(10) unsigned NULL DEFAULT '0' COMMENT '投票注册时间控制',
              `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '投票发起人',
              `app_type` smallint(5) unsigned NULL DEFAULT '0' COMMENT '投票类型',
              `expired_time` int(10) unsigned NULL DEFAULT '0' COMMENT '投票有效时间',
              `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '投票创建时间',
              PRIMARY KEY (`poll_id`),
              KEY `idx_createduserid_createdtime` (`created_userid`,`created_time`),
              KEY `idx_voternum` (`voter_num`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='投票基本信息表';
         */
        Schema::create('pw_app_poll', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->increments('poll_id')->comment('自增ID');
            $table->mediumInteger('voter_num')->unsigned()->nullable()->default(0)->comment('投票人数');
            $table->tinyInteger('isafter_view')->unsigned()->nullable()->default(0)->comment('是否投票后查看结果');
            $table->tinyInteger('isinclude_img')->unsigned()->nullable()->default(0)->comment('是否包含图片');
            $table->smallInteger('option_limit')->unsigned()->nullable()->default(0)->comment('投票选项控制');
            $table->integer('regtime_limit')->unsigned()->nullable()->default(0)->comment('投票注册时间控制');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('投票发起人');
            $table->smallInteger('app_type')->unsigned()->nullable()->default(0)->comment('投票类型');
            $table->integer('expired_time')->unsigned()->nullable()->default(0)->comment('投票有效时间');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('投票创建时间');

            $table->index(['created_userid', 'created_time']);
            $table->index('voter_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_app_poll');
    }
}
