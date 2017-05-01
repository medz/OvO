<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_link_relations`;
CREATE TABLE `pw_link_relations` (
  `lid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接id',
  `typeid` smallint(5) unsigned NOT NULL COMMENT '分类id',
  PRIMARY KEY (`lid`,`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接分类关系表';

 */

class PwLinkRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_link_relations', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_link_relations');
    }
}

