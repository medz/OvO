<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_pay_order`;
CREATE TABLE `pw_pay_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` char(30) NULL DEFAULT '',
  `price` decimal(8,2) NULL DEFAULT '0.00',
  `number` smallint(5) unsigned NULL DEFAULT '0',
  `state` tinyint(1) unsigned NULL DEFAULT '0',
  `payemail` varchar(60) NULL DEFAULT '',
  `paymethod` tinyint(3) unsigned NULL DEFAULT '0',
  `paytype` tinyint(3) unsigned NULL DEFAULT '0',
  `buy` int(10) unsigned NULL DEFAULT '0',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `extra_1` int(10) unsigned NULL DEFAULT '0',
  `extra_2` varchar(255) NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_orderno` (`order_no`),
  KEY `idx_createduserid` (`created_userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='支付订单表';

 */

class PwPayOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_pay_order', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_pay_order');
    }
}

