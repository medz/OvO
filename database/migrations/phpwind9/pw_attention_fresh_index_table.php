<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention_fresh_index`;
CREATE TABLE `pw_attention_fresh_index` (
  `fresh_id` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`fresh_id`),
  KEY `idx_tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新鲜事与帖子关联表';

 */

class PwAttentionFreshIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_attention_fresh_index', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
			
			$table->integer('fresh_id')->unsigned();
			$table->integer('tid')->unsigned()->nullable()->default(0);
			
			$table->primary('fresh_id');
			$table->index('tid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_fresh_index');
    }
}

