<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_windid_admin_auth`;
CREATE TABLE `pw_windid_admin_auth` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(15) NULL DEFAULT '' COMMENT '用户名',
  `roles` varchar(255) NULL DEFAULT '' COMMENT '角色',
  `created_time` int(10) unsigned NULL DEFAULT '0' COMMENT '创建时间',
  `modified_time` int(10) unsigned NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户权限角色表';

 */

class PwWindidAdminAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_windid_admin_auth', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            
            $table->smallInteger('id');
			$table->integer('uid')->nullable()->default(0)->comment('用户ID');
			$table->string('username', 15)->nullable()->default('')->comment('用户名');
			$table->string('roles', 255)->nullable()->default('')->comment('角色');
			$table->integer('creative_time')->unsigned()->nullable()->default(0)->comment('创建时间');
			$table->integer('modified_time')->unsigned()->nullable()->default(0)->comment('最后修改时间');
			
			$table->primary('id');
 			$table->index('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_windid_admin_auth');
    }
}

