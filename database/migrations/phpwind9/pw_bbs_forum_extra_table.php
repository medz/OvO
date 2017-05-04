<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_bbs_forum_extra`;
CREATE TABLE `pw_bbs_forum_extra` (
`fid` smallint(5) unsigned NOT NULL,
`seo_description` varchar(255) NULL DEFAULT '',
`seo_keywords` varchar(255) NULL DEFAULT '',
`settings_basic` text,
`settings_credit` text,
PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='帖子扩展信息表';

 */

class PwBbsForumExtraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_bbs_forum_extra', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->smallInteger('fid')->unsigned();
            $table->string('seo_description', 255)->nullable()->default('');
            $table->string('seo_keywords', 255)->nullable()->default('');
            $table->text('settings_basic');
            $table->text('settings_credit');
            $table->primary('fid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_bbs_forum_extra');
    }
}
