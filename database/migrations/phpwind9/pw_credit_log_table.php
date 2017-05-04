<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_credit_log`;
CREATE TABLE `pw_credit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ctype` varchar(8) NULL DEFAULT '',
  `affect` int(10) NULL DEFAULT '0',
  `logtype` varchar(40) NULL DEFAULT '',
  `descrip` varchar(255) NULL DEFAULT '',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_username` varchar(15) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_createduserid_createdtime` (`created_userid`,`created_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='积分日志表';

 */

class PwCreditLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_credit_log', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
      $table->increments('id')->unsigned();
      $table->string('ctype', 8)->nullable()->default('');
      $table->integer('affect')->unsigned()->nullable()->default(0);
      $table->string('logtype', 40)->nullable()->default('');
      $table->string('descrip', 255)->nullable()->default('');
      $table->integer('created_userid')->unsigned()->nullable()->default(0);
      $table->string('created_username', 15)->nullable()->default('');
      $table->integer('created_time')->unsigned()->nullable()->default(0);

      $table->primary('pid');
      $table->index(['created_userid', 'created_time']);

      });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_credit_log');
    }
}

