<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAdminRoleTable extends Migration
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

            DROP TABLE IF EXISTS `pw_admin_role`;
            CREATE TABLE `pw_admin_role` (
              `id` smallint(6) NOT NULL AUTO_INCREMENT,
              `name` varchar(15) NULL DEFAULT '' COMMENT '角色名',
              `auths` text COMMENT '权限点',
              `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
              `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '最后修改时间',
              PRIMARY KEY (`id`),
              KEY `idx_name` (`name`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台用户角色表';
        */
        Schema::create('pw_admin_role', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->increments('id');
            $table->string('name', 15)->nullable()->default('')->comment('角色名');
            $table->text('auths')->comment('权限点');
            $table->integer('created_time')->unsigned()->nullable()->default(0)->comment('创建时间');
            $table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('最后修改时间');

            $table->primary('id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_admin_role');
    }
}
