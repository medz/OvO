<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_style`;
CREATE TABLE `pw_style` (
  `app_id` char(20) NOT NULL,
  `iscurrent` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否默认',
  `style_type` char(10) NULL DEFAULT '' COMMENT '风格类型',
  `name` varchar(100) NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(100) NULL DEFAULT '' COMMENT '应用别名',
  `logo` varchar(100) NULL DEFAULT '' COMMENT '图标',
  `author_name` varchar(30) NULL DEFAULT '' COMMENT '作者名',
  `author_icon` varchar(100) NULL DEFAULT '' COMMENT '作者头像',
  `author_email` varchar(200) NULL DEFAULT '' COMMENT '作者email',
  `website` varchar(200) NULL DEFAULT '' COMMENT '作者网站',
  `version` varchar(50) NULL DEFAULT '' COMMENT '应用版本',
  `pwversion` varchar(50) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题风格表';

 */

class PwStyleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_style', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->char('app_id', 20);
            $table->tinyInteger('iscurrent')->unsigned()->nullable()->default(0)->comment('是否默认');
            $table->char('style_type', 10)->nullable()->default('')->comment('风格类型');
            $table->string('name', 100)->nullable()->default('')->comment('名称');
            $table->string('alias', 100)->nullable()->default('')->comment('应用别名');
            $table->string('logo', 100)->nullable()->default('')->comment('图标');
            $table->string('author_name', 30)->nullable()->default('')->comment('作者名');
            $table->string('author_icon', 100)->nullable()->default('')->comment('作者头像');
            $table->string('author_email', 200)->nullable()->default('')->comment('作者email');
            $table->string('website', 200)->nullable()->default('')->comment('作者网站');
            $table->string('version', 50)->nullable()->default('')->comment('应用版本');
            $table->string('pwversion', 50)->nullable()->default('');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('修改时间');
            $table->string('description', 255)->nullable()->default('')->comment('描述');
            $table->primary('app_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_style');
    }
}
