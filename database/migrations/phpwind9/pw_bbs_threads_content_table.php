<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*

DROP TABLE IF EXISTS `pw_bbs_threads_content`;
CREATE TABLE `pw_bbs_threads_content` (
`tid` int(10) unsigned NOT NULL,
`useubb` tinyint(1) unsigned NULL DEFAULT '0',
`usehtml` tinyint(1) unsigned NULL DEFAULT '0',
`aids` smallint(5) unsigned NULL DEFAULT '0',
`content` text,
`sell_count` mediumint(8) unsigned NULL DEFAULT '0',
`reminds` varchar(255) NULL DEFAULT '',
`word_version` smallint(5) unsigned NULL DEFAULT '0',
`tags` varchar(255) NULL DEFAULT '',
`ipfrom` varchar(255) NULL DEFAULT '',
`manage_remind` varchar(150) NULL DEFAULT '',
PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子内容表';

 */

class PwBbsThreadsContentTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		Schema::create('pw_bbs_threads_content', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->integer('tid')->unsigned()->nullable();
			$table->tinyInteger('useubb')->unsigned()->nullable()->default(0);
			$table->tinyInteger('usehtml')->unsigned()->nullable()->default(0);
			$table->smallInteger('aids')->unsigned()->nullable()->default(0);
			$table->text('content');
			$table->mediumInteger('sell_count')->unsigned()->nullable()->default(0);
			$table->string('reminds', 255)->nullable()->default('');
			$table->smallInteger('word_version')->unsigned()->nullable()->default(0);
			$table->string('tags', 255)->nullable()->default('');
			$table->string('ipfrom', 255)->nullable()->default('');
			$table->primary('tid');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_bbs_threads_content');
	}
}
