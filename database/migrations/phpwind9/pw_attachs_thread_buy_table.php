<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attachs_thread_buy`;
CREATE TABLE `pw_attachs_thread_buy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NULL DEFAULT '0',
  `created_userid` int(10) unsigned NULL DEFAULT '0',
  `created_time` int(10) unsigned NULL DEFAULT '0',
  `cost` mediumint(8) unsigned NULL DEFAULT '0',
  `ctype` tinyint(3) unsigned NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_aid` (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='帖子附件购买记录';

 */

class PwAttachsThreadBuyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_attachs_thread_buy', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->increments('id');
            $table->integer('aid')->unsigned()->nullable()->default(0);
            $table->integer('created_userid')->unsigned()->nullable()->default(0);
            $table->integer('created_time')->unsigned()->nullable()->default(0);
            $table->mediumInteger('cost')->unsigned()->nullable()->default(0);
            $table->tinyInteger('ctype')->unsigned()->nullable()->default(0);

            $table->primary('id');
            $table->index('aid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attachs_thread_buy');
    }
}
