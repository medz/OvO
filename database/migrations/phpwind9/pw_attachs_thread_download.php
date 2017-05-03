<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAttachsThreadDownload extends Migration
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
            DROP TABLE IF EXISTS `pw_attachs_thread_download`;
            CREATE TABLE `pw_attachs_thread_download` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
              `aid` int(10) unsigned NULL DEFAULT '0' COMMENT '附件aid',
              `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '下载人',
              `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '下载时间',
              `cost` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '花费积分数量',
              `ctype` tinyint(3) unsigned NULL DEFAULT '0' COMMENT '花费积分类型',
              PRIMARY KEY (`id`),
              KEY `idx_aid` (`aid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子附件下载记录' ;


        */
        Schema::create('pw_attachs_thread_download', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned()->comment('自增长ID');
            $table->integer('aid')->unsigned()->nullable()->default(0)->comment('附件aid');
            $table->integer('created_userid')->unsigned()->nullable()->default(0)->comment('下载人');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('下载时间');
            $table->mediumInteger('cost')->unsigned()->nullable()->default(0)->comment('花费积分数量');
            $table->tinyInteger('ctype')->unsigned()->nullable()->default(0)->comment('花费积分类型');
            $table->primary('id');
            $table->index('aid');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attachs_thread_download');
    }
}
