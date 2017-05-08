<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizQuestionQuizSetTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('quiz_question_quiz_set', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('quiz_question_id');
			$table->unsignedInteger('quiz_set_id');
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
		Schema::dropIfExists('quiz_question_quiz_set');
	}
}
