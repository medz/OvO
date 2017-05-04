<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_design_bak`;
CREATE TABLE `pw_design_bak` (
  `bak_type` tinyint(1) unsigned NOT NULL COMMENT '备份类型',
  `page_id` int(10) unsigned NOT NULL COMMENT '备份页面',
  `is_snapshot` tinyint(3) unsigned NOT NULL COMMENT '是否快照',
  `bak_info` MEDIUMTEXT COMMENT '备份信息',
  PRIMARY KEY (`page_id`,`bak_type`,`is_snapshot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='门户备份表';

 */

class PwDesignBakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_design_bak', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->tinyInteger('bak_type')->unsigned()->comment('备份类型');
            $table->integer('page_id')->unsigned()->comment('备份页面');
            $table->tinyInteger('is_snapshot')->unsigned()->comment('是否快照');
            $table->mediumText('is_snapshot')->comment('备份信息');
            $table->primary(['page_id', 'bak_type', 'is_snapshot']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_design_bak');
    }
}

