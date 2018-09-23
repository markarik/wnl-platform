<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPersonalDataTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_personal_data', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->unique();
			$table->string('personal_identity_number')->nullable();
			$table->string('identity_card_number')->nullable();
			$table->string('passport_number')->nullable();
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
		Schema::dropIfExists('user_personal_data');
	}
}
