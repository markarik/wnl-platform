<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLessonAvailabilities extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_lesson_availabilities', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->index();
			$table->unsignedInteger('lesson_id');
			$table->timestamp('start_date')->nullable();
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
		Schema::dropIfExists('user_lesson_availabilities');
	}
}
