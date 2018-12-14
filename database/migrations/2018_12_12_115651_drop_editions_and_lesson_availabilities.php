<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropEditionsAndLessonAvailabilities extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('editions');
		Schema::dropIfExists('lesson_availabilities');
		Schema::dropIfExists('user_progress');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('editions', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('course_id');
			$table->string('name');
			$table->timestamp('start_date');
			$table->timestamps();
		});
		Schema::create('lesson_availabilities', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('edition_id')->index();
			$table->unsignedInteger('lesson_id')->index();
			$table->timestamp('start_date');
			$table->timestamps();
		});
		Schema::create('user_progress', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('lesson_id')->index();
			$table->unsignedInteger('user_id')->index();
			$table->unsignedInteger('edition_id')->index();
			$table->string('route');
			$table->enum('status', [
				config('lessons.progress.in_progress'),
				config('lessons.progress.done'),
			]);
			$table->timestamps();
		});
	}
}
