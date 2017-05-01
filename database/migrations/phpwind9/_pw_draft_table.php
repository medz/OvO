<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_draft`;
CREATE TABLE `pw_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '草稿箱id',
  `created_userid` int(10) unsigned NULL DEFAULT '0' COMMENT '创建人',
  `title` varchar(100) NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`created_userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='草稿箱';

 */

class PwDraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_draft', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_draft');
    }
}

