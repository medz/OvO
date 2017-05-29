<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAppPollOptionTable extends Migration
{
    /**
     * 迁移运行.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function up()
    {
        /*
            DROP TABLE IF EXISTS `pw_app_poll_option`;
            CREATE TABLE `pw_app_poll_option` (
              `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '选项自增长ID',
              `poll_id` int(10) unsigned NULL DEFAULT '0' COMMENT '投票ID',
              `voted_num` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '该选项投票数',
              `content` varchar(255) NULL DEFAULT '' COMMENT '选项内容',
              `image` varchar(255) NULL DEFAULT '' COMMENT '选项图片',
              PRIMARY KEY (`option_id`),
              KEY `idx_pollid` (`poll_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='投票选项表';
         */
        Schema::create('pw_app_poll_option', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->increments('option_id')->comment('选项自增长ID');
            $table->integer('poll_id')->unsigned()->nullable()->default(0)->comment('投票ID');
            $table->mediumInteger('voted_num')->unsigned()->nullable()->default(0)->comment('该选项投票数');
            $table->string('content', 255)->nullable()->default('')->comment('选项内容');
            $table->string('image', 255)->nullable()->default('')->comment('选项图片');

            $table->index('poll_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_app_poll_option');
    }
}
