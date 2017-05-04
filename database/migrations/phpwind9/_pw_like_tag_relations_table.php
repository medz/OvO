<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_like_tag_relations`;
CREATE TABLE `pw_like_tag_relations` (
  `logid` int(10) unsigned NOT NULL COMMENT 'log标识ID',
  `tagid` int(10) unsigned NOT NULL COMMENT '标签ID',
  KEY `idx_logid` (`logid`),
  KEY `idx_tagid` (`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='喜欢分类-喜欢关系表';

 */

class PwLikeTagRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_like_tag_relations', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_like_tag_relations');
    }
}

