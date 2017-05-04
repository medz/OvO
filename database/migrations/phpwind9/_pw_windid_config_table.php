<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_config`;
CREATE TABLE `pw_windid_config` (
  `name` varchar(30) NOT NULL COMMENT '配置名字',
  `namespace` varchar(15) NOT NULL COMMENT '配置命名空间',
  `value` text COMMENT '值',
  `vtype` enum('string','array','object') NULL DEFAULT 'string' COMMENT '配置值类型',
  `descrip` text COMMENT '描述',
  PRIMARY KEY (`namespace`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='windid配置表';

 */

class PwWindidConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_config', function (Blueprint $table) {
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
        Schema::dropIfExists('pw_windid_config');
    }
}

