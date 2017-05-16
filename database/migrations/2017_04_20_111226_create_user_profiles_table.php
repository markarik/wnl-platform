<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_profiles', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->unique();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('public_email')->nullable();
			$table->string('public_phone')->nullable();
			$table->string('username')->nullable();
			$table->string('avatar')->nullable();
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
		Schema::dropIfExists('user_profiles');
	}
}
