<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCourseProgressTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_course_progress', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('lesson_id');
			$table->enum('status', [
				'in-progress',
				'complete',
			]);

			$table->unique(
				[
					'user_id',
					'lesson_id',
				],
				'unique_user_course_progress'
			);
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public
	function down()
	{
		Schema::dropIfExists('user_course_progress');
	}
}
