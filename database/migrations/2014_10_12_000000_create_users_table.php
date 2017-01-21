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
            $table->increments('id');
            $table->longText('first_name');
            $table->longText('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->longText('address')->nullable();
            $table->longText('zip')->nullable();
            $table->longText('city')->nullable();
			$table->longText('phone')->nullable();
			$table->tinyInteger('invoice')->default(0);
			$table->string('invoice_name')->nullable();
			$table->string('invoice_nip')->nullable();
			$table->string('invoice_address')->nullable();
			$table->string('invoice_zip')->nullable();
			$table->string('invoice_city')->nullable();
			$table->string('invoice_country')->nullable();
			$table->tinyInteger('consent_newsletter')->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
