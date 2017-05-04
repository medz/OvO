<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_credit_log_operate`;
CREATE TABLE `pw_credit_log_operate` (
`uid` int(10) unsigned NOT NULL,
`operate` varchar(40) NOT NULL,
`num` smallint(5) unsigned NULL DEFAULT '0',
`update_time` int(10) unsigned NULL DEFAULT '0',
PRIMARY KEY (`uid`,`operate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='积分操作日志表';

 */

class PwCreditLogOperateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_credit_log_operate', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('uid')->unsigned()->nullable();
            $table->string('operate', 40)->nullable();
            $table->smallInteger('num')->unsigned()->nullable()->default(0);
            $table->integer('update_time')->unsigned()->nullable()->default(0);
            $table->primary('uid', 'operate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_credit_log_operate');
    }
}
