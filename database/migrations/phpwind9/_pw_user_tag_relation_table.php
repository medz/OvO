<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_tag_relation`;
CREATE TABLE `pw_user_tag_relation` (
  `tag_id` int(10) unsigned NOT NULL COMMENT '个性标签ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`uid`,`tag_id`),
  KEY `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户个人标签与用户的关系表';

 */

class PwUserTagRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_user_tag_relation', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_user_tag_relation');
    }
}

