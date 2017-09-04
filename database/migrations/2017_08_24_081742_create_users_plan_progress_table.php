<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPlanProgressTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	 public function up()
	 {
		 Schema::create('users_plan_progress', function (Blueprint $table) {
			 $table->increments('id');
			 $table->unsignedInteger('plan_id');
			 $table->unsignedInteger('user_id');
			 $table->unsignedInteger('question_id');
			 $table->timestamp('resolved_at')->nullable();
		 });
	 }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users_plan_progress');
	}
}
