<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAppPollThreadTable extends Migration
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
            DROP TABLE IF EXISTS `pw_app_poll_thread`;
            CREATE TABLE `pw_app_poll_thread` (
              `tid` int(10) unsigned NOT NULL COMMENT '帖子ID',
              `poll_id` int(10) unsigned NULL DEFAULT '0' COMMENT '投票ID',
              `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '投票发起人',
              PRIMARY KEY (`tid`),
              KEY `idx_pollid` (`poll_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子投票关系表';
         */
        Schema::create('pw_app_poll_thread', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('tid')->unsigned()->nullable()->comment('帖子ID');
            $table->integer('poll_id')->unsigned()->nullable()->default(0)->comment('投票ID');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('投票发起人');

            $table->primary('tid');
            $table->index('poll_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_app_poll_thread');
    }
}
