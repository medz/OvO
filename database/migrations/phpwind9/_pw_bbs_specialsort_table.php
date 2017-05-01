<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_specialsort`;
CREATE TABLE `pw_bbs_specialsort` (
  `sort_type` char(16) NULL DEFAULT '',
  `fid` smallint(5) unsigned NULL DEFAULT '0',
  `tid` int(10) unsigned NULL DEFAULT '0',
  `pid` int(10) unsigned NULL DEFAULT '0',
  `extra` int(10) NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `end_time` int(10) unsigned NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子特殊排序表';

 */

class PwBbsSpecialsortTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_bbs_specialsort', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_bbs_specialsort');
    }
}

