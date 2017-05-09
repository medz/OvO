<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_notify_log`;
CREATE TABLE `pw_windid_notify_log` (
  `logid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nid` int(10) unsigned NULL DEFAULT '0',
  `appid` smallint(5) unsigned NULL DEFAULT '0',
  `complete` tinyint(3) unsigned NULL DEFAULT '0',
  `send_num` int(10) unsigned NULL DEFAULT '0',
  `reason` varchar(16) NULL DEFAULT '',
  PRIMARY KEY (`logid`),
  KEY `idx_complete` (`complete`),
  KEY `idx_appid` (`appid`),
  KEY `idx_nid` (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知发送记录表';

 */

class PwWindidNotifyLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_notify_log', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('logid')->unsigned();
            $table->integer('nid')->unsigned()->nullable()->default(0);
            $table->smallinteger('appid')->unsigned()->nullable()->default(0);
            $table->tinyinteger('complete')->unsigned()->nullable()->default(0);
            $table->integer('send_num')->unsigned()->nullable()->default(0);
            $table->string('reason', 16)->nullable()->default('');

            $table->primary('logid');
            $table->index('complete');
            $table->index('appid');
            $table->index('nid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_notify_log');
    }
}

