<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwBbsPostsReply extends Migration
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

        DROP TABLE IF EXISTS `pw_bbs_posts_reply`;
        CREATE TABLE `pw_bbs_posts_reply` (
          `pid` int(10) unsigned NOT NULL,
          `rpid` int(10) unsigned NULL DEFAULT '0',
          PRIMARY KEY (`pid`),
          KEY `idx_rpid_pid` (`rpid`,`pid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回复的回复';

        */
        Schema::create('pw_bbs_posts_reply', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('pid')->unsigned();
            $table->integer('rpid')->unsigned()->nullable()->default(0);
         
            $table->primary('pid');
            $table->index(['rpid', 'pid']);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_posts_reply');
    }
}
