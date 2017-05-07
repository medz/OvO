<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_tag_category_relation`;
CREATE TABLE `pw_tag_category_relation` (
  `tag_id` int(10) unsigned NOT NULL COMMENT '话题id',
  `category_id` smallint(5) unsigned NOT NULL COMMENT '分类id',
  PRIMARY KEY (`category_id`,`tag_id`),
  KEY `idx_tagid` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题分类关系表';

 */

class PwTagCategoryRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_tag_category_relation', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('tag_id')->unsigned()->comment('话题id');
            $table->smallinteger('category_id')->unsigned()->comment('分类id');

            $table->primary(['category_id', 'tag_id']);
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_tag_category_relation');
    }
}

