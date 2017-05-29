<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_cron`;
CREATE TABLE `pw_design_cron` (
  `module_id` int(10) unsigned NOT NULL COMMENT '模块ID',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`module_id`),
  KEY `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户更新队列表';

 */

class PwDesignCronTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_cron', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('module_id')->unsigned()->nullable()->comment('模块ID');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('更新时间');

            $table->primary('module_id');
            $table->index('created_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_cron');
    }
}
