<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_like_tag`;
CREATE TABLE `pw_like_tag` (
  `tagid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户ID',
  `tagname` varchar(20) NOT NULL COMMENT '标签名',
  `number` int(10) unsigned NULL DEFAULT '0' COMMENT '统计数',
  PRIMARY KEY (`tagid`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='喜欢分类表';

 */

class PwLikeTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_like_tag', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('tagid')->unsigned()->comment('标签ID');
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户ID');
            $table->string('tagname', 20)->nullable()->comment('标签名');
            $table->integer('number')->unsigned()->nullable()->default(0)->comment('统计数');

            $table->primary('tagid');
            $table->index('uid'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_like_tag');
    }
}

