<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_message`;
CREATE TABLE `pw_windid_message` (
  `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `from_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '发信人',
  `to_uid` int(10) unsigned NULL DEFAULT '0',
  `content` text COMMENT '内容',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`message_id`),
  KEY `idx_fromuid_touid` (`from_uid`,`to_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='消息内容表';

 */

class PwWindidMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_message', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_windid_message');
    }
}

