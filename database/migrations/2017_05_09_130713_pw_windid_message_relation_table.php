<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_message_relation`;
CREATE TABLE `pw_windid_message_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '关系id',
  `dialog_id` int(10) unsigned NULL DEFAULT '0' COMMENT '对话id',
  `message_id` int(10) unsigned NULL DEFAULT '0' COMMENT '私信id',
  `is_read` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否已读',
  `is_send` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '是否为发送者私信',
  PRIMARY KEY (`id`),
  KEY `idx_dialogid` (`dialog_id`),
  KEY `idx_messageid` ( `message_id` ),
  KEY `idx_isread` ( `is_read` ),
  KEY `idx_issend` ( `is_send` )

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息关系表';

 */

class PwWindidMessageRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_message_relation', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('关系id');
            $table->integer('dialog_id')->unsigned()->nullable()->default(0)->comment('对话id');
            $table->integer('message_id')->unsigned()->nullable()->default(0)->comment('私信id');
            $table->tinyinteger('is_read')->unsigned()->nullable()->default(0)->comment('是否已读');
            $table->tinyinteger('is_send')->unsigned()->nullable()->default(0)->comment('是否为发送者私信');

            $table->index('dialog_id');
            $table->index('message_id');
            $table->index('is_read');
            $table->index('is_send');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_message_relation');
    }
}
