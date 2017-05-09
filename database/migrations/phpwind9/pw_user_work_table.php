<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_work`;
CREATE TABLE `pw_user_work` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户ID',
  `company` varchar(100) NULL DEFAULT '' COMMENT '公司名字',
  `starty` smallint(4) NULL DEFAULT '0' COMMENT '开始年份',
  `endy` smallint(4) NULL DEFAULT '0' COMMENT '结束年份',
  `startm` tinyint(2) NULL DEFAULT '0' COMMENT '开始月份',
  `endm` tinyint(2) NULL DEFAULT '0' COMMENT '结束月份',
  PRIMARY KEY (`id`),
  KEY `idx_uid_starty_startm` (`uid`,`starty`,`startm`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户工作经历表';

 */

class PwUserWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_work', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned();
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户ID');
            $table->string('company', 100)->nullable()->default('')->comment('公司名字');
            $table->tsmallinteger('starty')->nullable()->default(0)->comment('开始年份');
            $table->tsmallinteger('endy')->nullable()->default(0)->comment('结束年份');
            $table->tinyInteger('startm')->nullable()->default(0)->comment('开始月份');
            $table->tinyInteger('endm')->nullable()->default(0)->comment('结束月份');
            
            $table->primary('id');
            $table->index(['uid', 'starty', 'startm']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_work');
    }
}

