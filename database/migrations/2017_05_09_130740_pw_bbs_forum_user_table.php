<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_forum_user`;
CREATE TABLE `pw_bbs_forum_user` (
`uid` int(10) unsigned NOT NULL,
`fid` smallint(5) unsigned NOT NULL,
`join_time` int(10) unsigned NULL DEFAULT '0',
PRIMARY KEY (`uid`,`fid`),
KEY `idx_fid_jointime` (`fid`,`join_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='版块会员';

 */

class PwBbsForumUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_bbs_forum_user', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned();
            $table->smallInteger('fid')->unsigned();
            $table->integer('join_time')->unsigned()->nullable()->default(0);

            $table->primary(['uid', 'fid']);
            $table->index(['fid', 'join_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_forum_user');
    }
}
