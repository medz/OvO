<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PwAdminAuth extends Migration
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

            DROP TABLE IF EXISTS `pw_admin_auth`;
            CREATE TABLE `pw_admin_auth` (
              `id` smallint(6) NOT NULL AUTO_INCREMENT,
              `uid` int(10) NULL DEFAULT '0' COMMENT '用户ID',
              `username` varchar(15) NULL DEFAULT '' COMMENT '用户名',
              `roles` varchar(255) NULL DEFAULT '' COMMENT '角色',
              `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
              `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '最后修改时间',
              PRIMARY KEY (`id`),
              KEY `idx_uid` (`uid`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户权限角色表';

            // created_time & modified_time 在迁移中使用 timestamps 替代，修改程序实现。

         */
        Schema::create('pw_admin_auth', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }

            $table->increments('id');
            $table->integer('uid')->nullable()->default(0)->comment('用户ID');
            $table->string('username', 15)->nullable()->default('')->comment('用户名');
            $table->string('roles', 255)->nullable()->default('')->comment('角色');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_admin_auth');
    }
}
