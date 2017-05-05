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
    public function up()
    {
        Schema::create('pw_domain', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->string('domain_key', 100)->comment('域名标识');
            $table->string('domain_type', 15)->nullable()->default('')->comment('域名类型');
            $table->string('domain', 15)->nullable()->default('')->comment('域名');
            $table->string('root', 45)->nullable()->default('')->comment('根域名');
            $table->char('first', 1)->nullable()->default('')->comment('域名首字母便于更新');
            $table->integer('id')->unsigned()->nullable()->default(0)->comment('部署应用的id值');
            $table->primary('domain_key');
            $table->index('domain_type');
        });
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
