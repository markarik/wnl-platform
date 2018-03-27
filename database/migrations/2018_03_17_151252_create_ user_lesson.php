<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLesson extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_lesson', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->index();
			$table->unsignedInteger('lesson_id')->index();
			$table->timestamp('start_date')->nullable();
			$table->timestamps();

			$table->unique(
				[
					'user_id',
					'lesson_id',
				],
				'user_lesson'
			);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_lesson');
	}
}
