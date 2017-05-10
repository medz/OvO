<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_medal_user`;
CREATE TABLE `pw_medal_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `medals` varchar(255) NULL DEFAULT '' COMMENT '拥有的勋章ID',
  `counts` int(10) unsigned NULL DEFAULT '0' COMMENT '勋章总数',
  `expired_time` int(10) unsigned NULL DEFAULT '0' COMMENT '最近的过期时间',
  PRIMARY KEY (`uid`),
  KEY `idx_counts` (`counts`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='勋章用户-统计表';

 */

class PwMedalUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_medal_user', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->nullable()->comment('用户ID');
            $table->string('medals', 255)->nullable()->default('')->comment('拥有的勋章ID');
            $table->integer('counts')->unsigned()->nullable()->default(0)->comment('勋章总数');
            $table->integer('expired_time')->unsigned()->nullable()->default(0)->comment('最近的过期时间');

            $table->primary('uid');
            $table->index('counts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_medal_user');
    }
}
