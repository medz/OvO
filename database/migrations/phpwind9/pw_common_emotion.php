<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PwCommonEmotion extends Migration {
	/**
	 * 迁移运行.
	 *
	 * @return void
	 * @author 流星 <lkddi@163.com>
	 */
	public function run() {
		/*

			  DROP TABLE IF EXISTS `pw_common_emotion`;
			  CREATE TABLE `pw_common_emotion` (
			    `emotion_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '表情ID',
			    `category_id` smallint(5) unsigned NULL DEFAULT '0' COMMENT '表情分类',
			    `emotion_name` varchar(20) NULL DEFAULT '' COMMENT '表情名称',
			    `emotion_folder` varchar(20) NULL DEFAULT '' COMMENT '所属文件夹',
			    `emotion_icon` varchar(50) NULL DEFAULT '' COMMENT '表情图标',
			    `vieworder` int(10) unsigned NULL DEFAULT '0' COMMENT '排序',
			    `isused` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否使用',
			    PRIMARY KEY (`emotion_id`),
			    KEY `idx_catid` (`category_id`),
			    KEY `idx_isused` (`isused`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表情数据表';
		*/
		Schema::create('pw_common_emotion', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->increments('emotion_id')->unsigned()->comment('表情ID');
			$table->smallInteger('category_id')->unsigned()->nullable()->default(0)->comment('表情分类');
			$table->string('emotion_name', 20)->nullable()->default('')->comment('表情名称');
			$table->string('emotion_folder', 20)->nullable()->default('')->comment('所属文件夹');
			$table->string('emotion_icon', 50)->nullable()->default('')->comment('表情图标');
			$table->integer('vieworder')->unsigned()->nullable()->default(0)->comment('排序');
			$table->tinyInteger('isused')->unsigned()->nullable()->default(1)->comment('是否使用');
			$table->primary('emotion_id');
			$table->index('category_id');
			$table->index('isused');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_common_emotion');
	}
}
