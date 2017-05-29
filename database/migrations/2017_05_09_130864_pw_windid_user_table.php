<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_user`;
CREATE TABLE `pw_windid_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(15) NULL DEFAULT '' COMMENT '用户名字',
  `email` varchar(80) NULL DEFAULT '' COMMENT 'Email',
  `password` char(32) NULL DEFAULT '' COMMENT '密码',
  `salt` char(6) NULL DEFAULT '' COMMENT '盐值',
  `safecv` char(8) NULL DEFAULT '' COMMENT '安全问题',
  `regdate` int(10) unsigned NULL DEFAULT '0' COMMENT '注册时间',
  `regip` varchar(20) NULL DEFAULT '' COMMENT '注册IP',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='windid用户基本信息表';

 */

class PwWindidUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_user', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('uid')->unsigned()->comment('用户ID');
            $table->string('username', 15)->nullable()->default('')->comment('用户名字');
            $table->string('email', 80)->nullable()->default('')->comment('Email');
            $table->char('password', 32)->nullable()->default('')->comment('密码');
            $table->char('salt', 6)->nullable()->default('')->comment('盐值');
            $table->char('safecv', 8)->nullable()->default('')->comment('安全问题');
            $table->integer('regdate')->unsigned()->nullable()->default(0)->comment('注册时间');
            $table->string('regip', 20)->nullable()->default('')->comment('安全问题');

            $table->unique('username');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_user');
    }
}
