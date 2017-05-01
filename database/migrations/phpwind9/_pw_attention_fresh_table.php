<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention_fresh`;
CREATE TABLE `pw_attention_fresh` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NULL DEFAULT '0',
  `src_id` int(10) unsigned NULL DEFAULT '0',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_createduserid_createdtime` (`created_userid`,`created_time`),
  KEY `idx_type_srcid` (`type`,`src_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='新鲜事主表';

 */

class PwAttentionFreshTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_attention_fresh', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_attention_fresh');
    }
}

