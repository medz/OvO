<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAttentionType extends Migration
{
    /**
     * 迁移运行.
     *
     * @return void
     * @author 流星 <lkddi@163.com>
     */
    public function run()
    {
        /*
          
          DROP TABLE IF EXISTS `pw_attention_type`;
          CREATE TABLE `pw_attention_type` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `uid` int(10) unsigned NULL DEFAULT '0',
            `name` varchar(30) NULL DEFAULT '',
            PRIMARY KEY (`id`),
            KEY `idx_uid` (`uid`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='关注分类表';

        */
        Schema::create('pw_attention_type', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->increments('id')->unsigned();
            $table->integer('uid')->unsigned()->nullable()->default(0);
            $table->string('name', 30)->nullable()->default('');
            $table->primary('id');
            $table->index('uid');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_type');
    }
}
