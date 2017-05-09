<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_user_info`;
CREATE TABLE `pw_windid_user_info` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `realname` varchar(20) NULL DEFAULT '',
  `icon` varchar(100) NULL DEFAULT '' COMMENT '头像---未用',
  `gender` tinyint(1) NULL DEFAULT '0' COMMENT '性别',
  `byear` smallint(4) unsigned NULL DEFAULT '0' COMMENT '出生年份',
  `bmonth` tinyint(2) unsigned NULL DEFAULT '0' COMMENT '出生月份',
  `bday` tinyint(2) unsigned NULL DEFAULT '0' COMMENT '出生日期',
  `hometown` int(10) NULL DEFAULT '0' COMMENT '家庭地址ID',
  `location` int(10) NULL DEFAULT '0' COMMENT '居住地ID',
  `homepage` varchar(128) NULL DEFAULT '' COMMENT '主页',
  `qq` varchar(12) NULL DEFAULT '' COMMENT 'QQ ',
  `aliww` varchar(30) NULL DEFAULT '' COMMENT '阿里旺旺',
  `mobile` varchar(16) NULL DEFAULT '' COMMENT '手机号码',
  `alipay` varchar(80) NULL DEFAULT '' COMMENT '支付宝',
  `msn` varchar(80) NULL DEFAULT '' COMMENT 'MSN',
  `profile` varchar(250) NULL DEFAULT '',
  PRIMARY KEY (`uid`),
  KEY `idx_bday` (`bday`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='windid用户扩展基本信息表二';

 */

class PwWindidUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_user_info', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->comment('用户ID');
            $table->string('realname', 20)->nullable()->default('');
            $table->string('icon', 20)->nullable()->default('')->comment('头像---未用');
            $table->tinyinteger('gender')->nullable()->default(0)->comment('性别');
            $table->smallinteger('byear')->unsigned()->nullable()->default(0)->comment('出生年份');
            $table->tinyinteger('bmonth')->unsigned()->nullable()->default(0)->comment('出生月份');
            $table->tinyinteger('bday')->unsigned()->nullable()->default(0)->comment('出生日期');
            $table->integer('hometown')->nullable()->default(0)->comment('家庭地址ID');
            $table->integer('location')->nullable()->default(0)->comment('居住地ID');
            $table->string('homepage', 128)->nullable()->default('')->comment('主页');
            $table->string('qq', 12)->nullable()->default('')->comment('QQ');
            $table->string('aliww', 30)->nullable()->default('')->comment('阿里旺旺');
            $table->string('mobile', 16)->nullable()->default('')->comment('手机号码');
            $table->string('alipay', 80)->nullable()->default('')->comment('支付宝');
            $table->string('msn', 80)->nullable()->default('')->comment('MSN');
            $table->string('profile', 250)->nullable()->default('');

            $table->primary('uid');
            $table->index('bday');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_user_info');
    }
}

