<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_digest_index`;
CREATE TABLE `pw_bbs_threads_digest_index` (
`tid` int(10) unsigned NOT NULL,
`fid` smallint(5) unsigned NULL DEFAULT '0',
`disabled` tinyint(3) unsigned NULL DEFAULT '0',
`cid` smallint(5) unsigned NULL DEFAULT '0',
`topic_type` int(10) unsigned NULL DEFAULT '0',
`created_time` int(10) unsigned NULL DEFAULT '0',
`lastpost_time` int(10) unsigned NULL DEFAULT '0',
`operator` varchar(15) NULL DEFAULT '',
`operator_userid` int(10) unsigned NULL DEFAULT '0',
`operator_time` int(10) unsigned NULL DEFAULT '0',
PRIMARY KEY (`tid`),
KEY `idx_cid_lastposttime` (`cid`,`lastpost_time`),
KEY `idx_fid_lastposttime_topictype` (`fid`,`lastpost_time`,`topic_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='精华帖子索引表';

 */

class PwBbsThreadsDigestIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_bbs_threads_digest_index', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('tid')->unsigned()->nullable();
            $table->smallInteger('fid')->unsigned()->nullable()->default(0);
            $table->tinyInteger('disabled')->unsigned()->nullable()->default(0);
            $table->smallInteger('cid')->unsigned()->nullable()->default(0);
            $table->integer('topic_type')->unsigned()->nullable()->default(0);
            $table->integer('created_time')->unsigned()->nullable()->default(0);
            $table->integer('lastpost_time')->unsigned()->nullable()->default(0);
            $table->string('operator', 15)->nullable()->default('');
            $table->integer('operator_userid')->unsigned()->nullable()->default(0);
            $table->integer('operator_time')->unsigned()->nullable()->default(0);

            $table->primary('tid');
            $table->index(['cid', 'lastpost_time']);
            $table->index(['fid', 'lastpost_time', 'topic_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_threads_digest_index');
    }
}
