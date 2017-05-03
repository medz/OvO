<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PwBbsThreadsOvertime extends Migration {
	/**
	 * 迁移运行.
	 *
	 * @return void
	 * @author 流星 <lkddi@163.com>
	 */
	public function run() {
		/*

			  DROP TABLE IF EXISTS `pw_bbs_threads_overtime`;
			  CREATE TABLE `pw_bbs_threads_overtime` (
			    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			    `tid` int(10) unsigned NULL DEFAULT '0',
			    `m_type` enum('topped','highlight') NOT NULL,
			    `overtime` int(10) unsigned NULL DEFAULT '0',
			    PRIMARY KEY (`id`),
			    UNIQUE KEY `idx_tid_mtype` (`tid`,`m_type`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子操作时间表';

		*/
		Schema::create('pw_bbs_threads_overtime', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->increments('id')->unsigned();
			$table->integer('tid')->unsigned()->nullable()->default(0);
			$table->m_type('m_type', ['topped', 'highlight'])->nullable();
			$table->integer('overtime')->unsigned()->nullable()->default(0);
			$table->primary('id');
			$table->unique(['tid', 'm_type']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_bbs_threads_overtime');
	}
}
