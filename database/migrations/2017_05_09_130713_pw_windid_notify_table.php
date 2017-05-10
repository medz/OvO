<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_notify`;
CREATE TABLE `pw_windid_notify` (
  `nid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` smallint(5) unsigned NULL DEFAULT '0',
  `operation` varchar(50) NULL DEFAULT '',
  `param` text COMMENT '消息参数',
  `timestamp` int(10) NULL DEFAULT '0',
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='通知队列表';

 */

class PwWindidNotifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_notify', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('nid')->unsigned();
            $table->smallinteger('appid')->unsigned()->nullable()->default(0);
            $table->string('operation', 50)->nullable()->default('');
            $table->text('param')->comment('消息参数');
            $table->integer('timestamp')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_notify');
    }
}
