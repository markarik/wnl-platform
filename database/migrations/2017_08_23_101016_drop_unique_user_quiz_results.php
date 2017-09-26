<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniqueUserQuizResults extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->dropUnique('unique_quiz_result');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->unique(
				[
					'user_id',
					'quiz_question_id',
				],
				'unique_quiz_result'
			);
		});
	}
}
