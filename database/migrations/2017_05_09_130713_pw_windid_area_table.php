<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_area`;
CREATE TABLE `pw_windid_area` (
  `areaid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `name` varchar(50) NULL DEFAULT '' COMMENT '地区名字',
  `joinname` varchar(100) NULL DEFAULT '' COMMENT '地区路径的cache地址',
  `parentid` mediumint(8) unsigned NULL DEFAULT '0' COMMENT '上级路径ID',
  `vieworder` smallint(5) unsigned NULL DEFAULT '0' COMMENT '顺序',
  PRIMARY KEY (`areaid`),
  KEY `idx_name` (`name`),
  KEY `idx_parentid_vieworder` (`parentid`,`vieworder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='统一地区库';

 */

class PwWindidAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_area', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->mediumIncrements('areaid')->unsigned()->comment('地址ID');
            $table->string('name', 50)->nullable()->default('')->comment('地区名字');
            $table->string('joinname', 100)->nullable()->default('')->comment('地区路径的cache地址');
            $table->mediuminteger('parentid')->unsigned()->nullable()->default(0)->comment('上级路径ID');
            $table->smallinteger('vieworder')->unsigned()->nullable()->default(0)->comment('顺序');

            $table->index('name');
            $table->index(['parentid', 'vieworder']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_area');
    }
}
