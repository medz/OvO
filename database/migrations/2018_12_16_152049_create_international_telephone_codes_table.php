<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalTelephoneCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_telephone_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->comment('International telephone code');
            $table->string('name', 100);
            $table->string('icon', 50);
            $table->timestamp('enabled_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('enabled_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('international_telephone_codes');
    }
}
