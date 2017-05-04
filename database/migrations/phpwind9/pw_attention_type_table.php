<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*

DROP TABLE IF EXISTS `pw_attention_type`;
CREATE TABLE `pw_attention_type` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`uid` int(10) unsigned NULL DEFAULT '0',
`name` varchar(30) NULL DEFAULT '',
PRIMARY KEY (`id`),
KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='关注分类表';

 */

class PwAttentionTypeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		Schema::create('pw_attention_type', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->increments('id')->unsigned();
			$table->integer('uid')->unsigned()->nullable()->default(0);
			$table->string('name', 30)->nullable()->default('');
			$table->primary('id');
			$table->index('uid');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_attention_type');
	}
}
