<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_message_dialog`;
CREATE TABLE `pw_windid_message_dialog` (
  `dialog_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '对话id',
  `to_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '收信人',
  `from_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '发信人',
  `unread_count` smallint(5) unsigned NULL DEFAULT '0' COMMENT '未读数',
  `message_count` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '总对话数量',
  `last_message` text COMMENT '最新对话',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`dialog_id`),
  UNIQUE KEY `idx_touid_fromuid` (`to_uid`,`from_uid`),
  KEY `idx_touid_modifiedtime` (`to_uid`,`modified_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息对话表';

 */

class PwWindidMessageDialogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_message_dialog', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('dialog_id')->unsigned()->comment('对话id');
            $table->integer('to_uid')->unsigned()->nullable()->default(0)->comment('收信人');
            $table->integer('from_uid')->unsigned()->nullable()->default(0)->comment('发信人');
            $table->smallinteger('unread_count')->unsigned()->nullable()->default(0)->comment('未读数');
            $table->mediuminteger('message_count')->unsigned()->nullable()->default(0)->comment('总对话数量');
            $table->text('last_message')->comment('最新对话');
            $table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('修改时间');

            $table->primary('dialog_id');
            $table->index(['to_uid', 'from_uid']);
            $table->index(['to_uid', 'modified_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_message_dialog');
    }
}
