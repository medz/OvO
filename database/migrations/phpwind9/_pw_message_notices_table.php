<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_message_notices`;
CREATE TABLE `pw_message_notices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知id',
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户uid',
  `typeid` smallint(5) unsigned NULL DEFAULT '0' COMMENT '类型id',
  `param` int(10) NULL DEFAULT '0' COMMENT '应用类型id',
  `is_read` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否已读',
  `is_ignore` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否忽略',
  `title` varchar(255) NULL DEFAULT '' COMMENT '标题',
  `extend_params` text COMMENT '扩展内容',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid_read_modifiedtime` (`uid`,`is_read`,`modified_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='通知表';

 */

class PwMessageNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_message_notices', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_message_notices');
    }
}

