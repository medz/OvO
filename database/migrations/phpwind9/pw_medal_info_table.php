<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_medal_info`;
CREATE TABLE `pw_medal_info` (
  `medal_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '勋章ID',
  `name` varchar(50) NULL DEFAULT '' COMMENT '勋章名称',
  `path` varchar(50) NULL DEFAULT '' COMMENT '勋章路径',
  `image` varchar(50) NULL DEFAULT '' COMMENT '勋章图片(系统勋章带路径)',
  `icon` varchar(50) NULL DEFAULT '' COMMENT '勋章图标(系统勋章带路径)',
  `descrip` varchar(255) NULL DEFAULT '' COMMENT '勋章简介',
  `medal_type` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章类型',
  `receive_type` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章获取类型',
  `medal_gids` varchar(50) NULL DEFAULT '' COMMENT '用户组',
  `award_type` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '勋章类型',
  `award_condition` smallint(5) unsigned NULL DEFAULT '0' COMMENT '勋章条件',
  `expired_days` smallint(5) unsigned NULL DEFAULT '0' COMMENT '有效期',
  `isopen` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否开启',
  `vieworder` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`medal_id`),
  KEY `idx_orderid` (`vieworder`),
  KEY `idx_isopen` (`isopen`),
  KEY `idx_award_type` (`award_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='勋章信息表';

 */

class PwMedalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_medal_info', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('medal_id')->unsigned()->comment('勋章ID');
            $table->string('name', 50)->nullable()->default('')->comment('勋章名称');
            $table->string('path', 50)->nullable()->default('')->comment('勋章路径');
            $table->string('image', 50)->nullable()->default('')->comment('勋章图片(系统勋章带路径)');
            $table->string('icon', 50)->nullable()->default('')->comment('勋章图标(系统勋章带路径)');
            $table->string('descrip', 50)->nullable()->default('')->comment('勋章简介');
            $table->tinyInteger('medal_type')->unsigned()->nullable()->default(1)->comment('勋章类型');
            $table->tinyInteger('receive_type')->unsigned()->nullable()->default(1)->comment('勋章获取类型');
            $table->string('medal_gids', 50)->nullable()->default('')->comment('用户组');
            $table->tinyInteger('award_type')->unsigned()->nullable()->default(1)->comment('勋章类型');
            $table->smallInteger('award_condition')->unsigned()->nullable()->default(0)->comment('勋章条件');
            $table->smallInteger('expired_days')->unsigned()->nullable()->default(0)->comment('有效期');
            $table->tinyInteger('isopen')->unsigned()->nullable()->default(1)->comment('是否开启');
            $table->tinyInteger('vieworder')->unsigned()->nullable()->default(0)->comment('排序');

            $table->primary('medal_id');
            $table->index('vieworder');
            $table->index('isopen');
            $table->index('award_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_medal_info');
    }
}
