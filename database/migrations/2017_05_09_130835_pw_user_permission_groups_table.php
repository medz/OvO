<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/*

DROP TABLE IF EXISTS `pw_user_permission_groups`;
CREATE TABLE `pw_user_permission_groups` (
  `gid` mediumint(8) unsigned NOT NULL COMMENT '用户组ID',
  `rkey` varchar(64) NOT NULL COMMENT '权限点',
  `rtype` enum('basic','system','systemforum') NULL DEFAULT 'basic' COMMENT '权限类型',
  `rvalue` text COMMENT '权限值',
  `vtype` enum('string','array') NULL DEFAULT 'string' COMMENT '权限值类型',
  PRIMARY KEY (`gid`,`rkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组权限表';

 */

class PwUserPermissionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pw_user_permission_groups', function (Blueprint $table) {
            if (env('DB_CONNECTION', false) === 'mysql') {
                $table->engine = 'InnoDB';
            }
            $table->mediuminteger('gid')->unsigned()->comment('用户组ID');
            $table->string('rkey', 64)->comment('权限点');
            $table->enum('rtype', ['basic', 'system', 'systemforum'])->nullable()->default('basic')->comment('权限类型');
            $table->text('rvalue')->comment('权限值');
            $table->enum('vtype', ['string', 'array'])->nullable()->default('string')->comment('权限值类型');
            $table->primary(['gid', 'rkey']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pw_user_permission_groups');
    }
}
