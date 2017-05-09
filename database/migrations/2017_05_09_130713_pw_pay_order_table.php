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
    public function up()
    {
        Schema::create('pw_pay_order', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned();
            $table->string('order_no', 30)->nullable()->default('')->comment('订单号');
            $table->decimal('price', 8, 2)->nullable()->default(0.00)->comment('金额');
            $table->smallInteger('number')->unsigned()->nullable()->default(0);
            $table->tinyInteger('state')->unsigned()->nullable()->default(0);
            $table->string('payemail', 60)->nullable()->default('');
            $table->tinyInteger('paymethod')->unsigned()->nullable()->default(0);
            $table->tinyInteger('paytype')->unsigned()->nullable()->default(0);
            $table->integer('buy')->unsigned()->nullable()->default(0);
            $table->integer('created_userid')->unsigned()->nullable()->default(0);
            $table->integer('created_time')->unsigned()->nullable()->default(0);
            $table->integer('extra_1')->unsigned()->nullable()->default(0);
            $table->string('extra_2', 255)->nullable()->default('');

            $table->primary('id');
            $table->index('order_no');
            $table->index('created_userid');
        });
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
