<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_tag_record`;
CREATE TABLE `pw_tag_record` (
  `tag_id` int(10) unsigned NULL DEFAULT '0' COMMENT '话题id',
  `is_reply` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否回复',
  `update_time` int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间',
  KEY `idx_tagid` (`tag_id`),
  KEY `idx_updatetime` (`update_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题排行统计表';

 */

class PwTagRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_tag_record', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('tag_id')->unsigned()->nullable()->default(0)->comment('话题id');
            $table->tinyInteger('is_reply')->unsigned()->nullable()->default(0)->comment('是否回复');
            $table->integer('update_time')->unsigned()->nullable()->default(0)->comment('更新时间');

            $table->index('tag_id');
            $table->index('update_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_tag_record');
    }
}
