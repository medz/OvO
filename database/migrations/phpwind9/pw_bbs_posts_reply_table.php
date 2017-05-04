<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*

DROP TABLE IF EXISTS `pw_bbs_posts_reply`;
CREATE TABLE `pw_bbs_posts_reply` (
`pid` int(10) unsigned NOT NULL,
`rpid` int(10) unsigned NULL DEFAULT '0',
PRIMARY KEY (`pid`),
KEY `idx_rpid_pid` (`rpid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回复的回复';

 */

class PwBbsPostsReplyTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
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
	public function down() {
		Schema::dropIfExists('pw_bbs_posts_reply');
	}
}
