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
            $table->string('phone', 50)->comment('User Phone Number');
            $table->string('international_telephone_code', 10)->comment('International telephone code');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email', 100)->nullable()->comment('User Email Address');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->comment('User Password');
            $table->timestamps();

            $table->unique('phone');
            $table->index('name');
            $table->index('international_telephone_code');
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
