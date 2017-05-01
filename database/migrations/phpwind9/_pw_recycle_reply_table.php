<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_recycle_reply`;
CREATE TABLE `pw_recycle_reply` (
  `pid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NULL DEFAULT '0',
  `fid` int(10) unsigned NULL DEFAULT '0',
  `operate_time` int(10) unsigned NULL DEFAULT '0',
  `operate_username` varchar(15) NOT NULL,
  `reason` text,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='回复回收站';

 */

class PwRecycleReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_recycle_reply', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_recycle_reply');
    }
}

