<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('用户ID');
            $table->string('login', 150)->nullable()->default(null)->comment('登录名');
            $table->string('email', 150)->nullable()->default(null)->comment('邮箱');
            $table->string('phone', 100)->nullable()->default(null)->comment('手机');
            $table->string('name')->nullable()->default(null)->comment('名字');
            $table->string('location')->nullable()->default(null)->comment('位置');
            $table->string('bio')->nullable()->default(null)->comment('个人简短传记');
            $table->string('gender')->nullable()->default(null)->comment('性别');
            $table->string('link')->nullable()->default(null)->comment('网址');

            $table->string('password')->comment('密码');
            $table->string('pw_password', 100)->nullable()->default(null)->comment('pw 老用户密码');
            $table->string('pw_salt', 100)->nullable()->default(null)->comment('pw 用户密码计算盐值');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
            $table->unique('email');
            $table->unique('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
