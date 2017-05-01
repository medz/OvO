<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention_recommend_record`;
CREATE TABLE `pw_attention_recommend_record` (
  `uid` int(10) unsigned NULL DEFAULT '0' COMMENT '用户uid',
  `recommend_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '潜在好友',
  `same_uid` int(10) unsigned NULL DEFAULT '0' COMMENT '共同好友',
  UNIQUE KEY `idx_uid_puid_suid` (`uid`,`recommend_uid`,`same_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='共同关注记录表';

 */

class PwAttentionRecommendRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_attention_recommend_record', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_recommend_record');
    }
}

