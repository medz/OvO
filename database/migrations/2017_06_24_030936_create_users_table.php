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
            $table->string('name')->nullable()->default(null)->comment('用户名');
            $table->string('email')->nullable()->default(null)->comment('邮箱');
            $table->string('phone')->nullable()->default(null)->comment('手机');
            $table->string('password')->comment('密码');
            $table->string('pw_password', 100)->nullable()->default(null)->comment('pw 老用户密码');
            $table->string('pw_salt', 100)->nullable()->default(null)->comment('pw 用户密码计算盐值');
            $table->rememberToken();
            $table->timestamps();

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
