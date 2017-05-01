<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_upgrade_log`;
CREATE TABLE `pw_upgrade_log` (
  `id` varchar(25) NOT NULL COMMENT '主键id',
  `type` tinyint(1) NULL DEFAULT '0' COMMENT '日志类型',
  `data` text COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='更新日志表';

 */

class PwUpgradeLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function run()
    {
        Schema::create('pw_upgrade_log', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
			
		$table->string('id', 25)->comment('主键id');
		$table->tinyInteger('type')->nullable()->default(0)->comment('日志类型');
		$table->text('data')->comment('内容');

		$table->primary('id');

        };
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_upgrade_log');
    }
}

