<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_addresses', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->unique();
			$table->longText('street')->nullable();
			$table->longText('zip')->nullable();
			$table->longText('city')->nullable();
			$table->longText('phone')->nullable();
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
		Schema::dropIfExists('user_addresses');
	}
}
