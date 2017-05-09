<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_hook`;
CREATE TABLE `pw_windid_hook` (
  `name` varchar(50) NOT NULL,
  `app_id` char(20) NULL DEFAULT '' COMMENT '应用id',
  `app_name` varchar(100) NULL DEFAULT '' COMMENT '应用名称',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '修改时间',
  `document` text COMMENT '钩子详细信息',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='钩子基本信息表';

 */

class PwWindidHookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_hook', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->string('name', 50);
            $table->string('app_id', 20)->nullable()->default('')->comment('应用id');
            $table->string('app_name', 100)->nullable()->default('')->comment('应用名称');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('修改时间');
            $table->text('document')->comment('钩子详细信息');

            $table->primary('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_hook');
    }
}

