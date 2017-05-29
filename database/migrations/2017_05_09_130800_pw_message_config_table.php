<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_message_config`;
CREATE TABLE `pw_message_config` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `privacy` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '关注人才能发私信',
  `notice_types` varchar(255) NULL DEFAULT '' COMMENT '通知忽略类型',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息用户配置表';

 */

class PwMessageConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_message_config', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->nullable()->comment('用户uid');
            $table->tinyInteger('typeid')->unsigned()->nullable()->default(0)->comment('关注人才能发私信');
            $table->string('notice_types', 255)->nullable()->default('')->comment('通知忽略类型');

            $table->primary('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_message_config');
    }
}
