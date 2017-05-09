<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_info`;
CREATE TABLE `pw_user_info` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `gender` tinyint(1) NULL DEFAULT '0' COMMENT '性别',
  `byear` smallint(5) unsigned NULL DEFAULT '0' COMMENT '出生年份',
  `bmonth` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '出生月份',
  `bday` tinyint(2) unsigned NULL DEFAULT '0' COMMENT '出生日期',
  `location` int(10) NULL DEFAULT '0' COMMENT '居住地ID',
  `location_text` varchar(100) NULL DEFAULT '',
  `hometown` int(10) NULL DEFAULT '0' COMMENT '家庭ID',
  `hometown_text` varchar(100) NULL DEFAULT '',
  `homepage` varchar(75) NULL DEFAULT '' COMMENT '主页',
  `qq` varchar(12) NULL DEFAULT '' COMMENT 'QQ 号码',
  `msn` varchar(40) NULL DEFAULT '' COMMENT 'MSN号码',
  `aliww` varchar(30) NULL DEFAULT '' COMMENT '阿里旺旺号码',
  `mobile` varchar(16) NULL DEFAULT '' COMMENT '手机号码',
  `alipay` varchar(30) NULL DEFAULT '' COMMENT '支付宝帐号',
  `bbs_sign` text COMMENT '个性签名',
  `profile` text COMMENT '个人简介',
  `regreason` varchar(200) NULL DEFAULT '' COMMENT '注册原因',
  `telphone` varchar(20) NULL DEFAULT '' COMMENT '电话号码',
  `address` varchar(100) NULL DEFAULT '' COMMENT '邮寄地址',
  `zipcode` varchar(10) NULL DEFAULT '' COMMENT '邮政编码',
  `secret` varchar(500) NULL DEFAULT '' COMMENT '隐私设置',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户扩展基本信息表二';

 */

class PwUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_info', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->comment('用户ID');
            $table->tinyinteger('gender')->nullable()->default(1)->comment('性别');
            $table->smallinteger('byear')->unsigned()->nullable()->default(1)->comment('出生年份');
            $table->tinyinteger('bmonth')->unsigned()->nullable()->default(1)->comment('出生月份');
            $table->tinyinteger('bday')->unsigned()->nullable()->default(1)->comment('出生日期');
            $table->integer('location')->nullable()->default(0)->comment('居住地ID');
            $table->string('location_text', 100)->nullable()->default('');
            $table->integer('hometown')->nullable()->default(0)->comment('积分字段1');
            $table->string('hometown_text', 100)->nullable()->default('');
            $table->string('homepage', 75)->nullable()->default('')->comment('主页');
            $table->string('qq', 12)->nullable()->default('')->comment('QQ 号码');
            $table->string('msn', 40)->nullable()->default('')->comment('MSN号码');
            $table->string('aliww', 30)->nullable()->default('')->comment('阿里旺旺号码');
            $table->string('mobile', 16)->nullable()->default('')->comment('手机号码');
            $table->string('alipay', 30)->nullable()->default('')->comment('支付宝帐号');
            $table->string('alipay', 30)->nullable()->default('')->comment('支付宝帐号');
            $table->text('bbs_sign')->comment('个性签名');
            $table->text('profile')->comment('个人简介');
            $table->string('regreason', 200)->nullable()->default('')->comment('注册原因');
            $table->string('telphone', 20)->nullable()->default('')->comment('电话号码');
            $table->string('address', 100)->nullable()->default('')->comment('邮寄地址');
            $table->string('zipcode', 10)->nullable()->default('')->comment('邮政编码');
            $table->string('secret', 500)->nullable()->default('')->comment('隐私设置');

            $table->primary('uid');
        });

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_info');
    }
}

