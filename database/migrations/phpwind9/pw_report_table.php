<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_report`;
CREATE TABLE `pw_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '举报id',
  `type` smallint(5) unsigned NULL DEFAULT '0' COMMENT '举报类型',
  `type_id` int(10) NULL DEFAULT '0' COMMENT '举报应用id',
  `content` varchar(100) NULL DEFAULT '' COMMENT '内容',
  `content_url` varchar(255) NULL DEFAULT '' COMMENT '内容链接',
  `author_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '作者',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '举报人',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '举报时间',
  `reason` varchar(255) NULL DEFAULT '' COMMENT '原因',
  `ifcheck` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否审核',
  `operate_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '举报处理人',
  `operate_time` int(10) unsigned NULL DEFAULT '0' COMMENT '举报处理时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='举报表';

 */

class PwReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_report', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('举报id');
            $table->smallInteger('type')->unsigned()->nullable()->default(0)->comment('举报类型');
            $table->integer('type_id')->unsigned()->nullable()->default(0)->comment('举报应用id');
            $table->string('content', 100)->nullable()->default('')->comment('内容');
            $table->string('content_url', 255)->nullable()->default('')->comment('内容链接');
            $table->integer('author_userid')->unsigned()->nullable()->default(0)->comment('作者');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('举报人');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('举报时间');
            $table->string('reason', 255)->nullable()->default('')->comment('原因');
            $table->tinyInteger('ifcheck')->unsigned()->nullable()->default(0)->comment('是否审核');
            $table->integer('operate_userid')->unsigned()->nullable()->default(0)->comment('举报处理人');
            $table->integer('operate_time')->unsigned()->nullable()->default(0)->comment('举报处理时间');

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_report');
    }
}

