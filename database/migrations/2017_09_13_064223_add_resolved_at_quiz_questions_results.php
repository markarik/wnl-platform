<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResolvedAtQuizQuestionsResults extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->timestamps();
		});

		DB::statement('ALTER TABLE user_quiz_results ADD id INTEGER NOT NULL UNIQUE AUTO_INCREMENT;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->dropTimestamps();
		});

		DB::statement('ALTER TABLE user_quiz_results DROP id;');
	}
}
