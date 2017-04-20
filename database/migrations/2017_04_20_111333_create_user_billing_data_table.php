<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBillingDataTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_billing_data', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->unique();
			$table->string('name')->nullable();
			$table->string('vat_id')->nullable();
			$table->string('address')->nullable();
			$table->string('zip')->nullable();
			$table->string('city')->nullable();
			$table->string('country')->nullable();
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
		Schema::dropIfExists('user_billing_data');
	}
}
