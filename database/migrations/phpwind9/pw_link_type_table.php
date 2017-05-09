<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_link_type`;
CREATE TABLE `pw_link_type` (
  `typeid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '友情链接分类ID',
  `typename` varchar(6) NULL DEFAULT '' COMMENT '分类名称',
  `vieworder` smallint(5) unsigned NULL DEFAULT '0' COMMENT '顺序',
  PRIMARY KEY (`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接分类表';

 */

class PwLinkTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_link_type', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
        $table->smallIncrements('typeid')->unsigned()->comment('友情链接分类ID');  
        $table->string('typename', 6)->nullable()->default('')->comment('分类名称');
        $table->smallInteger('vieworder')->unsigned()->nullable()->default(0)->comment('顺序');
        $table->primary('typeid');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_link_type');
    }
}

