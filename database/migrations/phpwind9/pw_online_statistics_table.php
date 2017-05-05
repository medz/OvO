<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_online_statistics`;
CREATE TABLE `pw_online_statistics` (
  `signkey` char(20) NOT NULL COMMENT '统计标识',
  `number` int(10) unsigned NULL DEFAULT '0' COMMENT '统计数量',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`signkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='在线-数据统计表';

 */

class PwOnlineStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_online_statistics', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->string('signkey', 20)->comment('统计标识');
            $table->integer('number')->unsigned()->nullable()->default(0)->comment('统计数量');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->primary('signkey');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_online_statistics');
    }
}

