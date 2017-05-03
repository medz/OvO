<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAttachs extends Migration
{
    /**
     * 迁移运行.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        /*
			DROP TABLE IF EXISTS `pw_attachs`;
	          CREATE TABLE `pw_attachs` (
	            `aid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件id',
	            `name` varchar(80) NULL DEFAULT '' COMMENT '文件名',
	            `type` varchar(15) NULL DEFAULT '' COMMENT '文件类型',
	            `size` int(10) unsigned NULL DEFAULT '0' COMMENT '文件大小',
	            `path` varchar(80) NULL DEFAULT '' COMMENT '存储路径',
	            `ifthumb` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否有缩略图',
	            `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '上传人用户id',
	            `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '上传时间',
	            `app` varchar(15) NULL DEFAULT '' COMMENT '来自应用类型',
	            `app_id` int(10) unsigned NULL DEFAULT '0' COMMENT '来自应用模块id',
	            `descrip` varchar(255) NULL DEFAULT '' COMMENT '文件描述',
	            PRIMARY KEY (`aid`),
	            KEY `idx_app_appid` (`app`,`app_id`)
	          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';         
        */
        Schema::create('pw_attachs', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('aid')->comment('附件id');
            $table->string('name', 80)->nullable()->comment('文件名');
            $table->string('type', 15)->nullable()->comment('文件类型');
            $table->integer('size')->unsigned()->nullable()->default(0)->comment('文件大小');
            $table->string('path', 80)->nullable()->default('')->comment('存储路径');
            $table->tinyInteger('ifthumb')->unsigned()->nullable()->default(0)->comment('是否有缩略图');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('上传人用户id');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('上传时间');
            $table->string('app', 15)->nullable()->default('')->comment('来自应用类型');
            $table->integer('app_id')>unsigned()->nullable()->default(0)->comment('来自应用模块id');
            $table->string('descrip', 255)->nullable()->default('')->comment('文件描述');
            $table->primary('aid');
            $table->index(['app', 'app_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attachs');
    }
}
