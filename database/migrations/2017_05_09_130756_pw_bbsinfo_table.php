<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbsinfo`;
CREATE TABLE `pw_bbsinfo` (
`id` smallint(3) unsigned NOT NULL auto_increment COMMENT '主键ID',
`newmember` varchar(15) NULL DEFAULT '' COMMENT '最新会员',
`totalmember` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '会员总数',
`higholnum` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '最高在线人数',
`higholtime` int(10) unsigned NULL DEFAULT '0' COMMENT '最高在线发生日期',
`yposts` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '昨日发帖数',
`hposts` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '最高日发帖数',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='论坛信息表';

 */

class PwBbsinfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_bbsinfo', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->smallIncrements('id')->unsigned()->comment('主键ID');
            $table->string('newmember', 15)->nullable()->default('')->comment('最新会员');
            $table->mediumInteger('totalmember')->unsigned()->nullable()->default(0)->comment('会员总数');
            $table->mediumInteger('higholnum')->unsigned()->nullable()->default(0)->comment('最高在线人数');
            $table->integer('higholtime')->unsigned()->nullable()->default(0)->comment('最高在线发生日期');
            $table->mediumInteger('yposts')->unsigned()->nullable()->default(0)->comment('昨日发帖数');
            $table->mediumInteger('hposts')->unsigned()->nullable()->default(0)->comment('最高日发帖数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbsinfo');
    }
}
