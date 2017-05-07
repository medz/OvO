<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_tag_attention`;
CREATE TABLE `pw_tag_attention` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `tag_id` int(10) unsigned NOT NULL COMMENT '话题id',
  `last_read_time` int(10) unsigned NULL DEFAULT '0' COMMENT '关注时间',
  PRIMARY KEY (`tag_id`,`uid`),
  KEY `idx_uid_lastreadtime` (`uid`,`last_read_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题关注表';

 */

class PwTagAttentionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_tag_attention', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->default(0)->comment('用户ID');
            $table->integer('tag_id')->unsigned()->default(0)->comment('话题id');
            $table->integer('last_read_time')->unsigned()->nullable()->default(0)->comment('关注时间');

            $table->primary(['tag_id', 'uid']);
            $table->index(['uid', 'last_read_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_tag_attention');
    }
}
