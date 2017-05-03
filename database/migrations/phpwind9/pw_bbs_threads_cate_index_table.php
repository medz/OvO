<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_cate_index`;
CREATE TABLE `pw_bbs_threads_cate_index` (
`tid` int(10) unsigned NOT NULL,
`cid` smallint(5) unsigned NULL DEFAULT '0',
`fid` smallint(5) unsigned NULL DEFAULT '0',
`disabled` tinyint(3) unsigned NULL DEFAULT '0',
`created_time` int(10) unsigned NULL DEFAULT '0',
`lastpost_time` int(10) unsigned NULL DEFAULT '0',
PRIMARY KEY (`tid`),
KEY `idx_cid_lastposttime` (`cid`,`lastpost_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子索引表-分类索引';

 */

class PwBbsThreadsCateIndexTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		Schema::create('pw_bbs_threads_cate_index', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->integer('tid')->unsigned()->nullable();
			$table->smallInteger('cid')->unsigned()->nullable()->default(0);
			$table->smallInteger('fid')->unsigned()->nullable()->default(0);
			$table->tinyInteger('disabled')->unsigned()->nullable()->default(0);
			$table->integer('created_time')->unsigned()->nullable()->default(0);
			$table->integer('lastpost_time')->unsigned()->nullable()->default(0);
			$table->primary('tid');
			$table->index(['cid', 'lastpost_time']);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_bbs_threads_cate_index');
	}
}
