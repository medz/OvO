<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_image`;
CREATE TABLE `pw_design_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `path` varchar(80) NULL DEFAULT '' COMMENT '原图片路径',
  `thumb` varchar(80) NULL DEFAULT '' COMMENT '缩略图路径',
  `width` int(10) unsigned NULL DEFAULT '0' COMMENT '缩略图宽',
  `height` int(10) unsigned NULL DEFAULT '0' COMMENT '缩略图高',
  `moduleid` int(10) unsigned NULL DEFAULT '0' COMMENT '所属模块',
  `data_id` int(10) unsigned NULL DEFAULT '0' COMMENT '门户数据ID',
  `sign` varchar(50) NULL DEFAULT '' COMMENT '标签key',
  `status` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '原图片状态1正常0不正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户异步缩略图片表';

 */

class PwDesignImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_design_image', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('附件ID');
            $table->string('path', 80)->nullable()->default('')->comment('原图片路径');
            $table->string('thumb', 80)->nullable()->default('')->comment('缩略图路径');
            $table->integer('width')->unsigned()->nullable()->default(0)->comment('缩略图宽');
            $table->integer('height')->unsigned()->nullable()->default(0)->comment('缩略图高');
            $table->integer('moduleid')->unsigned()->nullable()->default(0)->comment('所属模块');
            $table->integer('data_id')->unsigned()->nullable()->default(0)->comment('门户数据ID');
            $table->string('sign', 50)->nullable()->default('')->comment('标签key');
            $table->tinyInteger('status')->unsigned()->nullable()->default(1)->comment('原图片状态1正常0不正常');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_image');
    }
}
