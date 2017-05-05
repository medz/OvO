<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_log_login`;
CREATE TABLE `pw_log_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(10) unsigned NULL DEFAULT 0 COMMENT '用户ID',
  `username` varchar(15) NULL DEFAULT '' COMMENT '用户名字',
  `typeid` tinyint(3) unsigned NULL DEFAULT 0 COMMENT '错误类型',
  `created_time` int(10) unsigned NULL DEFAULT 0 COMMENT '尝试时间',
  `ip` varchar(40) NULL DEFAULT '' COMMENT '尝试IP',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_ip` (`ip`),
  KEY `idx_created_time` (`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='前台用户登录错误日志表';

 */

class PwLogLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_log_login', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('主键ID');
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户ID');
            $table->string('username', 15)->nullable()->default('')->comment('用户名字');
            $table->tinyInteger('typeid')->unsigned()->nullable()->default(0)->comment('错误类型');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('尝试时间');
            $table->string('ip', 40)->nullable()->default('')->comment('尝试IP');

            $table->primary('id');
            $table->index('username');
            $table->index('ip');
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
        Schema::dropIfExists('pw_log_login');
    }
}

