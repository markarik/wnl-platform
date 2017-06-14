<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuizResultsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_quiz_results', function (Blueprint $table) {
			$table->unsignedInteger('quiz_question_id');
			$table->unsignedInteger('quiz_answer_id');
			$table->unsignedInteger('user_id');

			$table->unique(
				[
					'user_id',
					'quiz_question_id',
				],
				'unique_quiz_result'
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
		Schema::dropIfExists('user_quiz_results');
	}
}
