<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAnnounce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
            // 原始 pw9 sql:
            DROP TABLE IF EXISTS `pw_announce`;
            CREATE TABLE `pw_announce` (
            `aid` smallint(6) NOT NULL AUTO_INCREMENT,
            `vieworder` smallint(6) NULL DEFAULT '0',
            `created_userid` int(10) unsigned NULL DEFAULT '0',
            `typeid` tinyint(1) NULL DEFAULT '0',
            `url` varchar(80) DEFAULT '',
              `subject` varchar(100) NULL DEFAULT '',
              `content` MEDIUMTEXT,
              `start_date` int(10) unsigned NULL DEFAULT '0',
               `end_date` int(10) unsigned NULL DEFAULT '0',

              PRIMARY
              KEY (`aid`),
              KEY `idx_startdate` (`start_date`),
              KEY `idx_vieworder` (`vieworder`)
              ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='公告管理表';


         */
                Schema::create('pw_announce', function (Blueprint $table) {
                    if (env('DB_CONNECTION', false) === 'mysql') {
                        $table->engine = 'InnoDB';
                    }

                    $table->smallInteger('aid')->
            $table->smallInteger('vieworder')->nullable()->default('');
                    $table->integer('created_userid')->unsigned()->default('');
                    $table->tinyInteger('typeid')->nullable()->default('');
                    $table->string('url', 80)->default('');
                    $table->string('subject', 100)->nullable()->default('');
                    $table->mediumText('content');
                    $table->integer('start_date')->unsigned()->nullable()->default('');
                    $table->integer('end_date')->unsigned()->nullable()->default('');
                    $table->primary('aid');
                    $table->index('staer_date');
                    $table->index('vieworder');
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_announce');
    }
}
