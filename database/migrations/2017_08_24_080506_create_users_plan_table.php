<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPlanTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	 public function up()
	 {
		 Schema::create('users_plans', function (Blueprint $table) {
			 $table->increments('id');
			 $table->unsignedInteger('user_id');
			 $table->unsignedInteger('slack_days_planned');
			 $table->unsignedInteger('slack_days_left');
			 $table->timestamp('start_date');
			 $table->timestamp('end_date')->nullable();
		 });
	 }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_plans');
	}
}
