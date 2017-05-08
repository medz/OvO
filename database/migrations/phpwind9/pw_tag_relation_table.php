<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_tag_relation`;
CREATE TABLE `pw_tag_relation` (
  `tag_id` int(10) unsigned NOT NULL COMMENT '话题id',
  `content_tag_id` int(10) unsigned NOT NULL COMMENT '内容id',
  `type_id` tinyint(3) unsigned NOT NULL COMMENT '应用分类id',
  `param_id` int(10) unsigned NOT NULL COMMENT '应用id',
  `ifcheck` tinyint(3) unsigned NULL DEFAULT '1' COMMENT '是否审核',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`type_id`,`param_id`,`content_tag_id`),
  KEY `idx_tagid_typeid` (`tag_id`,`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='话题内容关系表';

 */

class PwTagRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_tag_relation', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('tag_id')->unsigned()->default(0)->comment('话题id');
            $table->integer('content_tag_id')->unsigned()->nullable()->default(0)->comment('内容id');
            $table->tinyInteger('type_id')->nullable()->default(0)->comment('应用分类id');
            $table->integer('param_id')->unsigned()->nullable()->default(0)->comment('应用id');
            $table->tinyInteger('ifcheck')->nullable()->default(0)->comment('是否审核');
            $table->integer('ifcheck')->unsigned()->nullable()->default(0)->comment('创建时间');

            $table->primary(['type_id', 'param_id', 'content_tag_id']);
            $table->index(['tag_id', 'type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_tag_relation');
    }
}

