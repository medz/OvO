<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_domain`;
CREATE TABLE `pw_domain` (
  `domain_key` varchar(100) NOT NULL COMMENT '域名标识',
  `domain_type` varchar(15) NULL DEFAULT '' COMMENT '域名类型',
  `domain` varchar(15) NULL DEFAULT '' COMMENT '域名',
  `root` varchar(45) NULL DEFAULT '' COMMENT '根域名',
  `first` char(1) NULL DEFAULT '' COMMENT '域名首字母便于更新',
  `id` int(10) unsigned NULL DEFAULT '0' COMMENT '部署应用的id值',
  PRIMARY KEY (`domain_key`),
  KEY `idx_domaintype` (`domain_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='二级域名';

 */

class PwDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_domain', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_domain');
    }
}

