<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->increments('id')->comment('User ID');
            $table->string('name')->nullable()->comment('User Name');
            $table->string('avatar')->nullable()->comment('User Avatar');
            $table->string('international_telephone_code', 10)->comment('International telephone code');
            $table->string('phone', 50)->comment('User Phone Number');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('email', 50)->nullable()->comment('User Email');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->unique(['phone', 'international_telephone_code']);
            $table->index('phone');
            $table->index('name');
            $table->index('international_telephone_code');
            $table->index('email');
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
