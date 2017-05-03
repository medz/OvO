<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention_fresh_relations`;
CREATE TABLE `pw_attention_fresh_relations` (
  `uid` int(10) unsigned NULL DEFAULT '0',
  `fresh_id` int(10) unsigned NULL DEFAULT '0',
  `type` tinyint(3) unsigned NULL DEFAULT '0',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  KEY `idx_uid_createdtime` (`uid`,`created_time`),
  KEY `idx_freshid` (`fresh_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新鲜事关系表';

 */

class PwAttentionFreshRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_attention_fresh_relations', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('uid')->unsigned()->nullable()->default(0);
            $table->integer('fresh_id')->unsigned()->nullable()->default(0);
            $table->tinyInteger('type')->unsigned()->nullable()->default(0);
            $table->integer('created_userid')->unsigned()->nullable()->default(0);
            $table->integer('created_time')->unsigned()->nullable()->default(0);

            $table->index('uid', 'created_time');
            $table->index('fresh_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_fresh_relations');
    }
}
