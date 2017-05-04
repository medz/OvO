<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_sort`;
CREATE TABLE `pw_bbs_threads_sort` (
`fid` smallint(5) unsigned NOT NULL COMMENT '版块ID',
`tid` int(10) unsigned NOT NULL COMMENT '帖子ID',
`extra` int(10) NULL DEFAULT '0' COMMENT '扩展字段,如置顶1、2、3',
`sort_type` varchar(20) NULL DEFAULT '' COMMENT '排序类型',
`created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
`end_time` int(10) unsigned NULL DEFAULT '0' COMMENT '到期时间',
PRIMARY KEY (`fid`,`tid`),
KEY `idx_tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子排序表';

 */

class PwBbsThreadsSortTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_threads_sort', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->smallInteger('fid')->unsigned()->comment('版块ID');
            $table->integer('tid')->unsigned()->nullable()->default(0)->comment('帖子ID');
            $table->integer('extra')->unsigned()->nullable()->default(0)->comment('扩展字段,如置顶1、2、3');
            $table->string('sort_type', 20)->nullable()->default('')->comment('排序类型');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->integer('end_time')->unsigned()->nullable()->default(0)->comment('到期时间');
            $table->primary(['fid', 'tid']);
            $table->index('tid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_threads_sort');
    }
}
