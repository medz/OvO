<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_link`;
CREATE TABLE `pw_link` (
  `lid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接id',
  `vieworder` tinyint(3) NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(15) NULL DEFAULT '' COMMENT '名称',
  `url` varchar(255) NULL DEFAULT '' COMMENT '链接',
  `descrip` varchar(255) NULL DEFAULT '' COMMENT '描述',
  `logo` varchar(100) NULL DEFAULT '' COMMENT 'logo',
  `iflogo` tinyint(1) unsigned NULL DEFAULT '0' COMMENT '是否有logo',
  `ifcheck` tinyint(3) NULL DEFAULT '0' COMMENT '是否审核',
  `contact` varchar(100) NULL DEFAULT '' COMMENT '联系方式',
  PRIMARY KEY (`lid`),
  KEY `idx_ifcheck_vieworder` (`ifcheck`,`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接表';

 */

class PwLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_link', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->smallIncrements('lid')->unsigned()->comment('友情链接id');
            $table->tinyInteger('vieworder')->unsigned()->nullable()->default(0)->comment('排序');
            $table->string('name', 15)->nullable()->default('')->comment('名称');
            $table->string('url', 255)->nullable()->default('')->comment('链接');
            $table->string('descrip', 255)->nullable()->default('')->comment('描述');
            $table->string('logo', 100)->nullable()->default('')->comment('logo');
            $table->tinyInteger('iflogo')->unsigned()->nullable()->default(0)->comment('是否有logo');
            $table->tinyInteger('ifcheck')->unsigned()->nullable()->default(0)->comment('是否审核');
            $table->string('contact', 100)->nullable()->default('')->comment('联系方式');

            $table->primary('lid');
            $table->index(['ifcheck', 'vieworder']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_link');
    }
}

