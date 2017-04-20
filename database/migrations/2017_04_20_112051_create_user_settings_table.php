<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_settings', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->unique();
			$table->tinyInteger('consent_newsletter')->nullable();
			$table->tinyInteger('consent_account')->nullable();
			$table->tinyInteger('consent_order')->nullable();
			$table->tinyInteger('consent_terms')->nullable();
			$table->tinyInteger('notifications_email')->nullable();
			$table->tinyInteger('notifications_sms')->nullable();
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
		Schema::dropIfExists('user_settings');
	}
}
