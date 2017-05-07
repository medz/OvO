<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_space_domain`;
CREATE TABLE `pw_space_domain` (
  `domain` varchar(15) NOT NULL COMMENT '空间域名',
  `uid` INT(10) NULL DEFAULT 0 COMMENT '用户id',
  PRIMARY KEY  (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT = '空间域名表';

 */

class PwSpaceDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_space_domain', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->string('domain', 15)->nullable()->default('')->comment('空间域名');
            $table->integer('uid')->unsigned()->nullable()->default(0)->comment('用户id');

            $table->primary('domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_space_domain');
    }
}

