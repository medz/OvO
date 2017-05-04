<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_attention_type_relations`;
CREATE TABLE `pw_attention_type_relations` (
  `uid` int(10) unsigned NOT NULL,
  `touid` int(10) unsigned NOT NULL,
  `typeid` int(10) NOT NULL,
  PRIMARY KEY (`uid`,`touid`,`typeid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注分类关系表';

 */

class PwAttentionTypeRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_attention_type_relations', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->integer('uid')->unsigned();
            $table->integer('touid')->unsigned();
            $table->integer('typeid');

            $table->primary('uid', 'touid', 'typeid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_attention_type_relations');
    }
}
