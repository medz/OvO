<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_invite_code`;
CREATE TABLE `pw_invite_code` (
  `code` char(32) NOT NULL,
  `created_userid` int(10) NULL DEFAULT '0',
  `invited_userid` int(10) NULL DEFAULT '0',
  `ifused` tinyint(1) NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `modified_time` int(10) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`code`),
  KEY `idx_createduid` (`created_userid`),
  KEY `idx_inviteduid` (`invited_userid`),
  KEY `idx_createdtime` (`created_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邀请码记录表';

 */

class PwInviteCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_invite_code', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_invite_code');
    }
}

