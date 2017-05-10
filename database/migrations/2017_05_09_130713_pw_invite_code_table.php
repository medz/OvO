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
    public function up()
    {
        Schema::create('pw_invite_code', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->char('code', 32);
            $table->integer('created_userid')->nullable()->default(0);
            $table->integer('invited_userid')->nullable()->default(0);
            $table->tinyInteger('ifused')->nullable()->default(0);
            $table->integer('created_time')->unsigned()->nullable()->default(0);
            $table->integer('modified_time')->unsigned()->nullable()->default(0);

            $table->primary('code');
            $table->index('created_userid');
            $table->index('invited_userid');
            $table->index('created_time');
        });
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
