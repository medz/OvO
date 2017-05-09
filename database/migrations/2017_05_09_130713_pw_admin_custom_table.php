<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAdminCustomTable extends Migration
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

            DROP TABLE IF EXISTS `pw_admin_custom`;
            CREATE TABLE `pw_admin_custom` (
              `username` varchar(15) NOT NULL,
              `custom` text COMMENT '常用菜单项',
              PRIMARY KEY (`username`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台常用菜单表';

         */
        Schema::create('pw_admin_custom', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->string('username', 15);
            $table->text('custom')->comment('常用菜单项');
            $table->primary('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_admin_custom');
    }
}
