<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PwBbsThreadsCateIndex extends Migration {
	/**
	 * 迁移运行.
	 *
	 * @return void
	 * @author 流星 <lkddi@163.com>
	 */
	public function run() {
		/*
			  DROP TABLE IF EXISTS `pw_bbs_threads_hits`;
			  CREATE TABLE `pw_bbs_threads_hits` (
			    `tid` int(10) unsigned NOT NULL,
			    `hits` int(10) unsigned NULL DEFAULT '0',
			    PRIMARY KEY (`tid`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8  COMMENT='帖子点击记录表';
		*/
		Schema::create('pw_bbs_threads_hits', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->integer('tid')->unsigned()->nullable();
			$table->integer('hits')->unsigned()->nullable()->default(0);
			$table->primary('tid');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_bbs_threads_hits');
	}
}
