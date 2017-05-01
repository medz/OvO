<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_frag_template`;
CREATE TABLE `pw_frag_template` (
  `tpl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `frag_cid` smallint(5) unsigned NULL DEFAULT '0',
  `tpl_name` varchar(50) NULL DEFAULT '',
  `template` text,
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

 */

class PwFragTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_frag_template', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_frag_template');
    }
}

