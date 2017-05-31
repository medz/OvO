<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_posts`;
CREATE TABLE `pw_bbs_posts` (
`pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
`fid` smallint(5) unsigned NULL DEFAULT '0',
`tid` int(10) unsigned NULL DEFAULT '0',
`disabled` tinyint(1) unsigned NULL DEFAULT '0',
`ischeck` tinyint(3) unsigned NULL DEFAULT '1',
`ifshield` tinyint(1) unsigned NULL DEFAULT '0',
`replies` int(10) unsigned NULL DEFAULT '0',
`useubb` tinyint(1) unsigned NULL DEFAULT '0',
`usehtml` tinyint(1) unsigned NULL DEFAULT '0',
`aids` smallint(5) unsigned NULL DEFAULT '0',
`rpid` int(10) unsigned NULL DEFAULT '0',
`subject` varchar(100) NULL DEFAULT '',
`content` text,
`like_count` mediumint(8) unsigned NULL DEFAULT '0',
`sell_count` mediumint(8) unsigned NULL DEFAULT '0',
`created_time` int(10) unsigned NULL DEFAULT '0',
`created_username` varchar(15) NULL DEFAULT '',
`created_userid` int(10) unsigned NULL DEFAULT '0',
`created_ip` varchar(40) NULL DEFAULT '',
`reply_notice` tinyint(3) unsigned NULL DEFAULT '1',
`modified_time` int(10) unsigned NULL DEFAULT '0',
`modified_username` varchar(15) NULL DEFAULT '',
`modified_userid` int(10) unsigned NULL DEFAULT '0',
`modified_ip` varchar(40) NULL DEFAULT '',
`reminds` varchar(255) NULL DEFAULT '',
`word_version` smallint(5) unsigned NULL DEFAULT '0',
`ipfrom` varchar(255) NULL DEFAULT '',
`manage_remind` varchar(150) NULL DEFAULT '',
`topped` tinyint(3) unsigned NULL DEFAULT '0',
PRIMARY KEY (`pid`),
KEY `idx_tid_disabled_createdtime` (`tid`,`disabled`,`created_time`),
KEY `idx_disabled_createdtime` (`disabled`,`created_time`),
KEY `idx_createduserid_createdtime` ( `created_userid` , `created_time` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子回复表';

 */

class PwBbsPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_bbs_posts', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('pid')->unsigned();
            $table->smallInteger('fid')->unsigned()->nullable()->default(0);
            $table->integer('tid')->unsigned()->nullable()->default(0);
            $table->tinyInteger('disabled')->unsigned()->nullable()->default(0);
            $table->tinyInteger('ischeck')->unsigned()->nullable()->default(0);
            $table->tinyInteger('ifshield')->unsigned()->nullable()->default(0);
            $table->integer('replies')->unsigned()->nullable()->default(0);
            $table->tinyInteger('useubb')->unsigned()->nullable()->default(0);
            $table->tinyInteger('usehtml')->unsigned()->nullable()->default(0);
            $table->smallInteger('aids')->unsigned()->nullable()->default(0);
            $table->integer('rpid')->unsigned()->nullable()->default(0);
            $table->string('subject', 100)->nullable()->default('');
            $table->text('content');
            $table->mediumInteger('like_count')->unsigned()->nullable()->default(0);
            $table->mediumInteger('sell_count')->unsigned()->nullable()->default(0);
            $table->integer('created_time')->unsigned()->nullable()->default(0);
            $table->string('created_username', 15)->nullable()->default('');
            $table->integer('created_userid')->unsigned()->nullable()->default(0);
            $table->string('created_ip', 40)->nullable()->default('');
            $table->tinyInteger('reply_notice')->unsigned()->nullable()->default(0);
            $table->integer('modified_time')->unsigned()->nullable()->default(0);
            $table->string('modified_username', 15)->nullable()->default('');
            $table->integer('modified_userid')->unsigned()->nullable()->default(0);
            $table->string('modified_ip', 40)->nullable()->default('');
            $table->string('reminds', 255)->nullable()->default('');
            $table->smallInteger('word_version')->unsigned()->nullable()->default(0);
            $table->string('ipfrom', 255)->nullable()->default('');
            $table->string('manage_remind', 150)->nullable()->default('');
            $table->tinyInteger('topped')->unsigned()->nullable()->default(0);

            $table->index(['tid', 'disabled', 'created_time']);
            $table->index(['disabled', 'created_time']);
            $table->index(['created_userid', 'created_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_posts');
    }
}
