<?php

declare(strict_types=1);

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
            $table->increments('id')->comment('User Unique ID');
            $table->string('name')->comment('User Name');
            $table->string('phone')->comment('User Phone Number');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email')->nullable()->comment('User Email Address');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->comment('User Password');
            $table->timestamps();

            $table->unique('phone');
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
        Schema::dropIfExists('users');
    }
}
