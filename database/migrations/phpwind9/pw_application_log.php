<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAdminAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*

            // 原始 pw9 sql:

        DROP TABLE IF EXISTS `pw_application_log`;
        CREATE TABLE `pw_application_log` (
          `app_id` char(20) NULL DEFAULT '' COMMENT '应用id',
          `log_type` char(10) NULL DEFAULT '' COMMENT '日志类型',
          `data` text COMMENT '日志内容',
          `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
          `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
          UNIQUE KEY `app_id` (`app_id`,`log_type`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用安装日志表';

         */
        Schema::create('pw_application_log', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('app_id')->nullable()->default(0)->comment('应用id');
            $table->string('log_type', 10)->nullable()->default('')->comment('日志类型');
            $table->text('data')->comment('日志内容');
            $table->integer('created_time', 10)->nullable()->default('0')->comment('创建时间');
            $table->integer('modified_time', 10)->nullable()->default('0')->comment('修改时间');
            $table->unique(['app_id', 'log_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_application_log');
    }
}
