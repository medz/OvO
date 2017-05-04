<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PwCommonCron extends Migration {
	/**
	 * 迁移运行.
	 *
	 * @return void
	 * @author 流星 <lkddi@163.com>
	 */
	public function run() {
		/*

			  DROP TABLE IF EXISTS `pw_common_cron`;
			  CREATE TABLE `pw_common_cron` (
			    `cron_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '计划任务ID',
			    `subject` varchar(50) NULL DEFAULT '' COMMENT '计划任务名称',
			    `loop_type` varchar(10) NULL DEFAULT '' COMMENT '循环类型month/week/day/hour/now',
			    `loop_daytime` varchar(50) NULL DEFAULT '' COMMENT '循环类型时间（日-时-分）',
			    `cron_file` varchar(50) NULL DEFAULT '' COMMENT '计划任务执行文件',
			    `isopen` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否开启 0 否，1是，2系统任务',
			    `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '计划任务创建时间',
			    `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '计划任务上次执行结束时间',
			    `next_time` int(10) unsigned NULL DEFAULT '0' COMMENT '下一次执行时间',
			    PRIMARY KEY (`cron_id`),
			    KEY `idx_next_time` (`next_time`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='计划任务表';
		*/
		Schema::create('pw_common_cron', function (Blueprint $table) {
			if (env('DB_CONNECTION', false) === 'mysql') {
				$table->engine = 'InnoDB';
			}
			$table->increments('cron_id')->unsigned()->comment('计划任务ID');
			$table->string('subject', 50)->nullable()->default('')->comment('计划任务名称');
			$table->string('loop_type', 10)->nullable()->default('')->comment('循环类型month/week/day/hour/now');
			$table->string('loop_daytime', 50)->nullable()->default('')->comment('循环类型时间（日-时-分）');
			$table->string('cron_file', 50)->nullable()->default('')->comment('计划任务执行文件');
			$table->tinyInteger('isopen')->unsigned()->nullable()->default(1)->comment('是否开启 0 否，1是，2系统任务');
			$table->integer('created_time')->unsigned()->nullable()->default(0)->comment('计划任务创建时间');
			$table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('计划任务上次执行结束时间');
			$table->integer('next_time')->unsigned()->nullable()->default(0)->comment('下一次执行时间');
			$table->primary('cron_id');
			$table->index('next_time');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('pw_common_cron');
	}
}
