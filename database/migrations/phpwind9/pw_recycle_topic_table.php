<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_recycle_topic`;
CREATE TABLE `pw_recycle_topic` (
  `tid` int(10) unsigned NOT NULL,
  `fid` smallint(5) unsigned NULL DEFAULT '0',
  `operate_time` int(10) unsigned NULL DEFAULT '0',
  `operate_username` varchar(15) NOT NULL,
  `reason` text,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题回收站';

 */

class PwRecycleTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_recycle_topic', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->integer('tid')->unsigned();
            $table->smallinteger('fid')->unsigned()->nullable()->default(0);
            $table->integer('operate_time')->unsigned()->nullable()->default(0);
            $table->string('operate_username', 15)->nullable();
            $table->text('reason');
            $table->primary('pid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_recycle_topic');
    }
}

