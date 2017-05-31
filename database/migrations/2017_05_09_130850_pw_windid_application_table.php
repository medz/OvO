<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_application`;
CREATE TABLE `pw_windid_application` (
  `app_id` char(20) NOT NULL COMMENT '应用id',
  `name` varchar(100) NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(100) NULL DEFAULT '' COMMENT '别名',
  `logo` varchar(100) NULL DEFAULT '' COMMENT '应用logo',
  `author_name` varchar(30) NULL DEFAULT '' COMMENT '作者名',
  `author_icon` varchar(100) NULL DEFAULT '' COMMENT '作者头像',
  `author_email` varchar(200) NULL DEFAULT '' COMMENT '作者email',
  `website` varchar(200) NULL DEFAULT '' COMMENT '开发者网站',
  `version` varchar(50) NULL DEFAULT '' COMMENT '应用版本',
  `pwversion` varchar(50) NULL DEFAULT '',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  `status` tinyint(1) NULL DEFAULT '0',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='本地应用信息表';

 */

class PwWindidApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_application', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->char('app_id', 20)->comment('应用id');
            $table->string('name', 100)->nullable()->default('')->comment('名称');
            $table->string('alias', 100)->nullable()->default('')->comment('别名');
            $table->string('logo', 100)->nullable()->default('')->comment('应用logo');
            $table->string('author_name', 30)->nullable()->default('')->comment('作者名');
            $table->string('author_icon', 100)->nullable()->default('')->comment('作者头像');
            $table->string('author_email', 200)->nullable()->default('')->comment('作者email');
            $table->string('website', 200)->nullable()->default('')->comment('开发者网站');
            $table->string('version', 50)->nullable()->default('')->comment('应用版本');
            $table->string('pwversion', 50)->nullable()->default('');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('修改时间');
            $table->tinyinteger('status')->nullable()->default(0);
            $table->string('description', 255)->nullable()->default('')->comment('描述');

            $table->primary('app_id');
            $table->index('alias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_application');
    }
}
