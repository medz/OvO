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
        }
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

