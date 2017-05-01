<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_seo`;
CREATE TABLE `pw_seo` (
  `mod` varchar(15) NOT NULL COMMENT '模块名',
  `page` varchar(20) NOT NULL COMMENT '页面名',
  `param` varchar(20) NOT NULL COMMENT '参数名',
  `title` varchar(255) NULL DEFAULT '' COMMENT '名称',
  `keywords` varchar(255) NULL DEFAULT '' COMMENT '关键词',
  `description` varchar(255) NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`mod`,`page`,`param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='seo';

 */

class PwSeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_seo', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_seo');
    }
}

